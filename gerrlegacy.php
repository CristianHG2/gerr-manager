#!/usr/bin/php
<?php

class CLIColors {
	private $foreground_colors = array();
	private $background_colors = array();

	public function __construct() {
		// Set up shell colors
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';

		$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
	}

	// Returns colored string
	public function color($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public function getForegroundColors() {
		return array_keys($this->foreground_colors);
	}

	// Returns all background color names
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}
}

class IO
{
	const ERROR = ['red', 'black', 'ERROR'];
	const INFO = ['white', 'blue', 'INFO'];
	const WARN = ['black', 'yellow', 'WARN'];

	static function out($text, $macro)
	{
		$cli = new CLIColors;
	    echo $cli->color($text, $macro[0], $macro[1]);
	}

    static function log($text, $mod = null)
    {
		if (is_null($mod)) { $mod = self::INFO; }
        self::out('['.$mod[2].']['.date('m/d/Y h:i a').'] '.$text.PHP_EOL, $mod);
    }

	static function error($text)
	{
		self::log($text, self::ERROR);
	}

	static function usage()
	{
    	self::error('Not enough arguments, correct usage:'); echo PHP_EOL;
	    self::error('gerr project new [domain] [--ssl][-S] [--laravel][-L] [--db][-D] - Creates new virtual site, and optionally, issues an SSL certificate, installs Laravel and/or creates a database');
	    self::error('gerr project full [domain] - Alias of the above command, but with all the options pre-enabled');
    	self::error('gerr project ws [domain] [address] [?upstream] -- Creates a new websocket upstream on an existing Nginx virtual site, optional upstream naming, random string by default');
	    return;
	}

	static function hasOpt($short, $long)
	{
		if (array_search("-$short", $argv) !== false || array_search("-$long", $argv) !== false) {
			return true;
		}

		return false;
	}
}

class Nginx
{
	static function sites()
	{
		return new NginxSite;
	}
}

class NginxSite
{
	public function create($domain)
	{
	}
}

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

			if (IO::hasOpt('S', 'ssl')) { $ssl = true; }
			if (IO::hasOpt('L', 'laravel')) { $laravel = true; }
			if (IO::hasOpt('D', 'db')) { $db = true; }
		break;
	}
}

/* EOL */

IO::out(PHP_EOL, 'black', 'black');
