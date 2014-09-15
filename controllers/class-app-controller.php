<?php

class App_Controller extends Framework_Controller {

	public function __construct(){
		//echo 'Framework controller contruct<pre>';
		//print_r($this);
		//echo '</pre>';
	}

	public static function init(){
		//echo 'test';
		//print_r(static::$components);

		parent::init();
	}
}