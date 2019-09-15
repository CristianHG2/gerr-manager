<?php

class Db
{
    static function create($dbname)
    {
        $json = file_get_contents('config.json');
        $json = json_decode($json);

        IO::log('Creating database '.$dbname.', please have your credentials for '.$json->dbuser.' ready');

        exec('mysql -u '.$json->dbuser.' -p -e "CREATE DATABASE '.$dbname.'"');
    }
}
