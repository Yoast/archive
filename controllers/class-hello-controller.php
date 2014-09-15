<?php

class Framework_Hello_Controller extends App_Controller {

	public static $components;

	public static function init(){
		self::$components = array('WordPress');

		parent::init();
	}

	/**
	 * Test controller hello function
	 */
	public function index(){
		//echo 'Index controller output<pre>';
		//print_r($this);
		//echo $this->Hello->test_model();
	}

}