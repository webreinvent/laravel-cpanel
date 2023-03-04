<?php
/**
 * CPanel
 *
 * @author: Pradeep Kumar
 * This package uses cPanel API 2.0
 */

namespace WebReinvent\CPanel;

use Config;

class CPanel {

    protected $config;
    protected $protocol;
    protected $domain;
    protected $port;
    protected $user;
    protected $token;


    //-----------------------------------------------------

    public function __construct($cpanel_domain=null, $cpanel_api_token=null, $cpanel_username=null, $protocol='https', $port=2083)
    {

        $this->config = Config::get('cpanel');

        if(isset($cpanel_domain))
        {
            $this->protocol = $protocol;
            $this->port = $port;
            $this->domain = $cpanel_domain;
            $this->username = $cpanel_username;
            $this->token = $cpanel_api_token;
        } else{
            $this->protocol = $this->config['protocol'];
            $this->domain = $this->config['domain'];
            $this->port = $this->config['port'];
            $this->username = $this->config['username'];
            $this->token = $this->config['api_token'];
        }


    }

    //-----------------------------------------------------

    public function createSubDomain($subdomain, $rootdomain, $dir)
    {
        $module = "SubDomain";
        $function = "addsubdomain";
        $parameters = array(
            'domain'        => $subdomain,
            'rootdomain'    => $rootdomain,
            'canoff'        => 0,
            'dir'           => $dir,
            'disallowdot'   => 0
        );
        return $this->call($module, $function, $parameters);
    }
    //-----------------------------------------------------

    //-----------------------------------------------------

    public function createDatabase($database_name)
    {
        $module = "Mysql";
        $function = "create_database";
        $parameters = array(
            'name'    => $database_name
        );
        return $this->call($module, $function, $parameters);
    }

    //-----------------------------------------------------
    public function deleteDatabase($database_name)
    {
        $module = "Mysql";
        $function = "delete_database";
        $parameters = array(
            'name'    => $database_name
        );
        return $this->call($module, $function, $parameters);
    }
    //-----------------------------------------------------
    public function listDatabases()
    {
        $module = "Mysql";
        $function = "list_databases";
        $parameters = array(
        );
        return $this->call($module, $function, $parameters);
    }
    //-----------------------------------------------------
    public function createDatabaseUser($username, $password)
    {
        $module = "Mysql";
        $function = "create_user";
        $parameters = array(
            'name'    => $username,
            'password'    => $password,
        );
        return $this->call($module, $function, $parameters);
    }

    //-----------------------------------------------------
    public function deleteDatabaseUser($username)
    {
        $module = "Mysql";
        $function = "delete_user";
        $parameters = array(
            'name'    => $username
        );
        return $this->call($module, $function, $parameters);
    }
    //-----------------------------------------------------
    public function setAllPrivilegesOnDatabase($database_user, $database_name)
    {
        $module = "Mysql";
        $function = "set_privileges_on_database";
        $parameters = array(
            'user'    => $database_user,
            'database'    => $database_name,
            'privileges'    => 'ALL PRIVILEGES',
        );
        return $this->call($module, $function, $parameters);
    }
    //-----------------------------------------------------


    //-----------------------------------------------------
    public function callUAPI($Module, $function, $parameters_array = array())
    {
        return $this->call($Module, $function, $parameters_array);
    }
    //-----------------------------------------------------

    public function call($module, $function, $args = array())
    {
        $parameters = '';
        if ( count($args) > 0 ) {
            foreach( $args as $key => $value ) {
                $parameters .= '&' . $key . '=' . urlencode($value);
            }
        }

        $url = $this->protocol.'://'.$this->domain . ':' . $this->port . '/execute/' . $module;
        $url .= "/".$function;

        if(count($args) > 0)
        {
            $url .= '?'. $parameters;
        }

        $headers = array(
            "Authorization: cpanel " . $this->username . ':' . $this->token,
            "cache-control: no-cache"
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->port,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => $headers,
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $curl_res = curl_exec($curl);
        $err = curl_error($curl);
        $err_no = curl_errno($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($curl_res, 0, $header_size);
        $body = substr($curl_res, $header_size);

        curl_close($curl);

        $curl_response_decoded = json_decode($curl_res);

        $response['inputs']['url'] = $url;

        $response['curl_response'] = [
            'curl_response' => $curl_res,
            'curl_response_decoded' => $curl_response_decoded,
            'header_size' => $header_size,
            'headers' => $headers,
            'header' => $header,
            'body' => $body,
            'error' => $err,
            'err_no' => $err_no,
        ];



        if (!empty($err_no)) {

            $response['status'] = 'failed';
            $response['errors'] = [$err];
            return $response;

        } if(isset($curl_response_decoded->errors)
        && count($curl_response_decoded->errors) > 0)
    {
        $response['status'] = 'failed';

        if(is_object($curl_response_decoded->errors))
        {
            $curl_response_decoded->errors = (array)$curl_response_decoded->errors;
        }

        if(is_array($curl_response_decoded->errors))
        {
            $response['errors'] = $curl_response_decoded->errors;
        } else{
            $response['errors'] = [$curl_response_decoded->errors];
        }
        return $response;

    } else {

        if(isset($res) && isset($res->status) && $res->status == 0)
        {
            $response['status'] = 'failed';
            $response['errors'] = [$res->errors];
            return $response;
        } else
        {
            $response['data'] = json_decode($curl_res);
            $response['status'] = 'success';
            return $response;
        }
    }


    }

    //-----------------------------------------------------

}
