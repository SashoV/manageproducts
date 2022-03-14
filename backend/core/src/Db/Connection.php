<?php

namespace src\Db;

class Connection extends \PDO
{  
    public function __construct($dsn = "mysql:host=".DBHOST.";dbname=".DBNAME, $username = DBUSER, $password = DBPASSWORD, $options = [])
    {
        $default_options = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        $options = array_replace($default_options, $options);
        parent::__construct($dsn, $username, $password, $options);
    }

}