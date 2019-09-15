#!/usr/bin/php
<?php

require __DIR__.'/install.php';
require __DIR__.'/colors.php';
require __DIR__.'/io.php';
require __DIR__.'/nginx.php';
require __DIR__.'/db.php';

/* Initialize GERR */

if ( count($argv) < 2 ) {
    return IO::usage();
}

/* Verify Setup */

$config = file_get_contents(__DIR__.'/config.json');
$config = json_decode($config);

if (!is_object($config) || !isset($config->init)) {
    IO::log('Initializing GERR before continuing');
	install();

	if (!is_object($config)) {
		$config = new \stdClass;
	}

	$config->init = true;

	file_put_contents(__DIR__.'/config.json', json_encode($config));
}

/* Naming */

$command = $argv[1];

if ($command === 'verify') {
    install();
    return;
}

$subcommand = $argv[2];

/* Handler */

if ($command === 'project') {
	switch ($subcommand) {
		case 'new':
			$ssl = $laravel = $db = false;

			if (IO::hasOpt('S', 'ssl', $argv)) {
				$ssl = true;
			}

			if (IO::hasOpt('L', 'laravel', $argv)) {
				$laravel = true;
			}

			if (IO::hasOpt('D', 'db', $argv)) {
				$db = true;
			}

			$site = NginxSite::create($argv[3]);

			if ($ssl) {
				$site->ssl();
			}

			if ($laravel) {
				$site->laravel();
			}

			if ($db) {
				$basename = explode('.', $argv[3])[0];
				Db::create($basename);
			}
		break;
		case 'verify':
			install();
		break;
	}
}

/* EOL */

//IO::out(PHP_EOL, 'black', 'black');

