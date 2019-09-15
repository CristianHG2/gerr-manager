<?php

require __DIR__.'/colors.php';
require __DIR__.'/io.php';
require __DIR__.'/nginx.php';

/* Initialize GERR */

if ( count($argv) <= 2 ) {
        return IO::usage();
}

/* Naming */

$command = $argv[1];
$subcommand = $argv[2];

/* Handler */

if ($command === 'project') {
        switch ($subcommand) {
                case 'new':
                        $ssl = $laravel = $db = false;

                        if (IO::hasOpt('S', 'ssl', $argv)) { $ssl = true; }
                        if (IO::hasOpt('L', 'laravel', $argv)) { $laravel = true; }
                        if (IO::hasOpt('D', 'db', $argv)) { $db = true; }

			Nginx::sites()->create($argv[3]);
                break;
        }
}

/* EOL */

//IO::out(PHP_EOL, 'black', 'black');

