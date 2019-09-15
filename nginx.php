<?php

define('NGINX_ROOT', '/etc/nginx/conf.d');
define('WEB_ROOT', '/var/www');

class Nginx
{
    const SITE_TEMPLATE = __DIR__.'/data/nginx.site.template';
    const SITE_UPGRADE = __DIR__.'/data/upgrade.site.template';
    const UPSTREAM_TOPBLOCK = __DIR__.'/data/upstream.site.template';
	const UPSTREAM_LOCATION = __DIR__.'/data/upstreamlocblock.site.template';
	
	static function site($site)
	{
		return new NginxSite($site);
	}
}

class NginxSite
{
    protected $domain = null;
    protected $paths = null;

    public function __construct($domain)
    {
        if (strpos($domain, 'www.') !== false) {
            IO::error('Do not include the www naming convention, GERR is not able to interact properly with this when setting up SSL');
        }

	if (!file_exists(NGINX_ROOT.'/'.$domain.'.conf')) {
	    IO::error("The $domain virtual site does not exist");
	}

	$this->paths = (object)[
	    'config' => NGINX_ROOT.'/'.$domain.'.conf',
	    'root'   => WEB_ROOT.'/'.$domain.'/'
	];

	$this->domain = $domain;
    }

    public function ssl()
    {
		$domain = $this->domain;
		IO::log('Issuing SSL certificate');
		passthru('sudo certbot --nginx -d '.$domain.' -d www.'.$domain);
    }

    public function laravel()
    {
		IO::log('Creating new laravel project');
		passthru('cd '.$this->paths->root);
		passthru('composer create-project --prefer-dist laravel/laravel .');
    }

    public function fixPermissions()
    {
		IO::log('Fixing permissions');
        exec('sudo chmod 775 '.WEB_ROOT.'/'.$this->domain);
        exec('sudo chown webmaster:webusers '.WEB_ROOT.'/'.$this->domain);
    }

    static function create($domain)
    {
		if (strpos($domain, 'www.') !== false) {
			IO:error('Do not include the www naming convention, GERR is not able to interact properly with this when setting up SSL');
		}

		IO::log('Creating new NGINX virtual site');

		$template = file_get_contents(Nginx::SITE_TEMPLATE);

		if (file_exists(NGINX_ROOT.'/'.$domain.'.conf') || file_exists(WEB_ROOT.'/'.$domain.'/')) {
				IO::error("Please ensure that there are not directories or nginx config files named $domain before continuing");
			return;
		}

		$template = str_replace('{domain}', $domain, $template);
		$template = str_replace('{root}', WEB_ROOT.'/'.$domain, $template);

		IO::log("Creating webroot ".WEB_ROOT.'/'.$domain);
		mkdir(WEB_ROOT.'/'.$domain);

		IO::log('Setting permissions');
		exec('sudo chmod 775 '.WEB_ROOT.'/'.$domain);
		exec('sudo chown webmaster:webusers '.WEB_ROOT.'/'.$domain);

		IO::log("Creating Nginx config file ".NGINX_ROOT.'/'.$domain.'.conf');
		file_put_contents($domain.'.conf', $template);
		exec('sudo mv '.$domain.'.conf '.NGINX_ROOT.'/'.$domain.'.conf');

		IO::log('Testing and restarting Nginx');

		exec('sudo nginx -t');
		exec('sudo service nginx restart');

		exec('sudo service nginx restart');

		IO::log('Done');

		return new self($domain);
    }
}

