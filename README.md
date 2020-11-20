# Laravel CPanel

> Laravel Package for CPanel UAPI  
  
Please consider starring the project to show your :heart: and support.  

> This laravel package allows you to manage you CPanel based hosting using CPanel UAPI. 

Some practical usages are:
- Programmatically create database, sub domains, emails or accounts etc
- Programmatically create database users
- Programmatically set privileges on database of any user

Learn more about at [CPanel UAPI](https://documentation.cpanel.net/display/DD/Guide+to+UAPI)

## Installation 

#### Step 1) Install the Package
Use following composer command to install the package
```bash  
composer require webreinvent/laravel-cpanel  
```
or
Add `webreinvent/laravel-cpanel` as a requirement to `composer.json`:

```
{
    ...
    "require": {
        ...
        "webreinvent/laravel-cpanel": "dev-master"
    },
}
```

Update composer:

```
$ composer update
```
#### Step 2) Register the ServiceProvider
Add following service provider in `config/app.php`  
```php  
/*  
 * Package Service Providers...  
 */ 
 'providers' => [  
        //...  
        WebReinvent\CPanel\CPanelServiceProvider::class,   
        //...  
    ],
```

#### Step 3) Publish Configurations
Run following command:
```
php artisan vendor:publish --provider="WebReinvent\CPanel\CPanelServiceProvider" --tag=config
```
#### Step 4) Set CPanel details in `.env`
```
CPANEL_DOMAIN= 
CPANEL_PORT=
CPANEL_API_TOKEN=
CPANEL_USERNAME=
```
or

```php
$cpanel = new CPanel($cpanel_domain=null, $cpanel_api_token=null, $cpanel_username=null, $protocol='https', $port=2083);
```

To generate `CPANEL_API_TOKEN`, login to the `CPanel >> SECURITY >> Manage API Tokens >> Create`.

## Usages & available methods 
Make sure you import:
```php
use WebReinvent\CPanel\CPanel;
```

#### To Create Database
Database name should be prefixed with cpanel username `cpanelusername_databasename`

If your CPanel username is `foo` then your database name 
| should be `foo_website`.

```php
$cpanel = new CPanel();
$response = $cpanel->createDatabase('cpanelusername_databasename');
```
Find More Details at [CPanel UAPI - Mysql::create_database](https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Mysql::create_database)

#### To Delete Database

```php
$cpanel = new CPanel();  
$response = $cpanel->deleteDatabase('cpanelusername_databasename');
```

[CPanel UAPI - Mysql::delete_database](https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Mysql%3A%3Adelete_database)

#### To Get List of All Databases in the CPanel

```php
$cpanel = new CPanel();  
$response = $cpanel->listDatabases();
```
#### To Create Database User

```php
$cpanel = new CPanel();  
$response = $cpanel->createDatabaseUser($username, $password);
```
#### To Delete Database User

```php
$cpanel = new CPanel();  
$response = $cpanel->deleteDatabaseUser($username);
```

#### To Give All Privileges to a Database User On a Database

```php
$cpanel = new CPanel();  
$response = $cpanel->setAllPrivilegesOnDatabase($database_user, $database_name);
```

Those were the available method but you can also call all the method available at  [CPanel UAPI](https://documentation.cpanel.net/display/DD/Guide+to+UAPI) using following method:
```php
$cpanel = new CPanel();  
$response = $cpanel->callUAPI($Module, $function, $parameters_array);
```
Example if you want to add new `ftp` account, documetation is available at [CPanel UAPI - Ftp::add_ftp](https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Ftp%3A%3Aadd_ftp) then use the method as represented below:
```php
$cpanel = new CPanel();  
$Module = 'Ftp';
$function = 'add_ftp';
$parameters_array = [
'user'=>'ftp_username',
'pass'=>'ftp_password', //make sure you use strong password
'quota'=>'42',
];
$response = $cpanel->callUAPI($Module, $function, $parameters_array);
```
## Support us  
  
[WebReinvent](https://www.webreinvent.com) is a web agency based in Delhi, India. You'll find an overview of all our open source projects [on github](https://github.com/webreinvent).  
  
## License  
  
The MIT License (MIT). Please see [License File](LICENSE) for more information.  
 
