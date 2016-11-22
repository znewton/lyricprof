<?php

include_once('Config.php');

class ViewRenderer
{
	private $config = null;

	public function __construct()
	{
		$this->config = new Config();
		$this->config = $this->config->getViewConfig();
	}

	public function renderView($viewFile)
	{
		include_once('ComponentRenderer.php');
		echo $this->renderHead();
		echo $this->renderNavbar();
		echo $this->renderDrawer();
		echo '<main>';
		set_include_path('');
		include 'views/'.$viewFile;
		echo '</main>';
		echo $this->renderFooter();
	}

	private function renderHead(){

		return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>LyricProf</title>
	<meta name="description" content="My own framework with my own theme" />
	<meta name="keywords" content="framework, theme, php, js, css, html" />
	<meta name="author" content="znewton" />
	<link rel="stylesheet" href="/lyricprof/lib/css/view.css">
	<link rel="stylesheet" href="/lyricprof/lib/css/components.css">
	<link rel="stylesheet" href="/lyricprof/lib/css/content.css">
	<link rel="icon" href="/lyricprof/lib/favicon-music.ico">
	<link rel="stylesheet" href="/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
	<script src="/lyricprof/lib/js/myFramework.js"></script>
	
	
    <link rel="stylesheet" href="/lyricprof/css/custom.css" />
    <script type="text/javascript" src="/lyricprof/lib/angular.min.js"></script>
    <script type="text/javascript" src="/lyricprof/lib/angular-sanitize.min.js"></script>
    <script type="text/javascript" src="/lyricprof/lib/jquery-3.1.0.min.js"></script>
    <script type="text/javascript" src="/lyricprof/js/app.js"></script>
    <script type="text/javascript" src="/lyricprof/js/controller.js"></script>
    <script type="text/javascript" src="/lyricprof/js/ngEnter.js"></script>
</head>
HTML;
	}

	private function renderNavbar(){
		$links = [];
		foreach ($this->config['nav_links'] as $link){
			$links[] = <<<HTML
<a href="{$link['href']}">{$link['label']}</a>
HTML;
		}
		$links = implode("\n",$links);
//        $logo = '<img src="/lib/NewtonLogo.svg"">';
//        $logo = '<div class="svg-wrapper">'. file_get_contents('NewtonLogo.svg',FILE_USE_INCLUDE_PATH).'</div>';
//		$logo = file_get_contents('NewtonLogo.svg',FILE_USE_INCLUDE_PATH);
		$logo = 'LyricProf';
		return <<<HTML
<nav id="navbar">
	<div class="left-nav">
		<a href="/" class="home-link">
			{$logo}
		</a>
	</div>
	<div class="right-nav">
		 {$links}
	</div>
</nav>
HTML;

	}
	private function renderDrawer(){
		$links = [];
		foreach ($this->config['nav_links'] as $link){
			$links[] = <<<HTML
<a href="{$link['href']}">{$link['label']}</a>
HTML;
		}
		$links = implode("\n",$links);
		return <<<HTML
<div id="ham-menu"><span></span><span></span><span></span></div>
<div id="drawer-indicator"></div>
<nav id="drawer">
	{$links}
</nav>
HTML;
	}
	private function renderDrawerLink(){

	}
	private function renderFooter(){
//        $logo = '<img src="/lib/NewtonLogo.svg"">';
		$logo = file_get_contents('NewtonLogo.svg',FILE_USE_INCLUDE_PATH);
		$socialLinks = '';
		foreach ($this->config['footer_social'] as $link)
		{
			$socialLinks .= <<<HTML
<a href="{$link['href']}">{$link['label']}</a>
HTML;
		}

		return <<<HTML
<footer>
<div class="footer-section footer-info">
	<a href="/">{$logo}</a>
	<a href="mailto:znewton13@gmail.com">znewton13@gmail.com</a>
</div>
<div class="footer-section footer-social">
	{$socialLinks}
</div>
</footer>
HTML;
	}
}