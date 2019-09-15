<?php

class IO
{
        const ERROR = ['red', 'black', 'ERROR'];
        const INFO = ['white', 'blue', 'INFO'];
        const WARN = ['black', 'yellow', 'WARN'];

        static function out($text, $macro)
        {
                $cli = new CLIColors;
            	echo $cli->color($text, $macro[0], $macro[1]);
				echo $cli->color(' ', 'black', 'black');
				echo PHP_EOL;
        }

	    static function log($text, $mod = null)
    	{
            if (is_null($mod)) { $mod = self::INFO; }
        	self::out('['.$mod[2].']['.date('m/d/Y h:i a').'] '.$text, $mod);
	    }

	static function confirm($question)
	{
	    $out = readline($question.' (Y/N)');

	    if (strtoupper(trim($out)) === 'Y') {
		return true;
	    }

	    return false;
	}

	static function abort()
	{
	    self::error('ABORT');
	    exit;
	}

        static function error($text)
        {
    	    self::log($text, self::ERROR);
        }

        static function usage()
        {
        	self::error('Not enough arguments, correct usage:');
            self::error('gerr project new [domain] [--ssl][-S] [--laravel][-L] [--db][-D] - Creates new virtual site, and optionally, issues an SSL certificate, installs Laravel and/or creates a database');
            //self::error('gerr project full [domain] - Alias of the above command, but with all the options pre-enabled');
        	//self::error('gerr project ws [domain] [address] [?upstream] -- Creates a new websocket upstream on an existing Nginx virtual site, optional upstream naming, random string by default');
            return;
        }

        static function hasOpt($short, $long, &$argv)
        {
            if (array_search("-$short", $argv) !== false || array_search("-$long", $argv) !== false) {
                    return true;
            }

            return false;
        }
}


