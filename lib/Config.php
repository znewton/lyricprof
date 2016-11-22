<?php

class Config
{
	private $routeConfig = [
		'about' => [
			'action' => [
				'view' => 'about.phtml',
			],
		],
	];
	function getRouteConfig(){
		return $this->routeConfig;
	}

	private $viewConfig = [
		'nav_links' => [
			'about' => [
				'label' => 'About',
				'href' => '/about'
			],
		],
		'drawer_links' => [],
		'footer_info' => [],
		'footer_social' => [
		    'github' => [
		        'label' => 'znewton',
                'href' => 'https://github.com/znewton'
            ],
		    'facebook' => [
		        'label' => 'Zach Newton',
                'href' => 'https://www.facebook.com/zach.newton.16'
            ],
		    'twitter' => [
		        'label' => 'Figgynewts6',
                'href' => 'https://twitter.com/Figgynewts6'
            ],
		    'instagram' => [
		        'label' => 'figgynewts6',
                'href' => 'https://www.instagram.com/figgynewts6'
            ],
        ],
	];

	function getViewConfig(){
		return $this->viewConfig;
	}
}