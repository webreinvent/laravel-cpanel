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

    public function __construct()
    {
        $this->config = Config::get('cpanel');

        $this->protocol = $this->config['protocol'];
        $this->domain = $this->config['domain'];
        $this->port = $this->config['port'];
        $this->username = $this->config['username'];
        $this->token = $this->config['api_token'];

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
        $function = "delete_database";
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
                $parameters .= '&' . $key . '=' . $value;
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

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {

            $response['status'] = 'failed';
            $response['errors'] = $err;
            $response['inputs']['url'] = $url;

        } else {

            $res = json_decode($response);

            $response = [];
            if(isset($res) && isset($res->status) && $res->status == 0)
            {
                $response['status'] = 'failed';
                $response['errors'][] = $res->errors;
                $response['inputs']['url'] = $url;
            } else
            {
                $response['status'] = 'success';
                $response['data'] = $res;
                $response['inputs']['url'] = $url;
            }
        }

        return $response;
    }

    //-----------------------------------------------------

}