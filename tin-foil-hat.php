<?php
/*
Plugin Name: Tin Foil Hat by Yoast
Description: This plugin helps you write awesome conspiring content.
Version: 1.4
Author: Team Yoast
Author URI: https://yoast.com
License: GPL v3
*/

define('YOAST_TIN_FOIL_HAT', __FILE__);

class Yoast_TinFoilHat
{
	public function add_hooks()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Enqueues the pluginscripts.
	 */
	public function enqueue_scripts()
	{
		// only enqueue on x-edit page.
		wp_enqueue_script('yoast-tfh', plugins_url('js/dist/factCheck-010.js', YOAST_TIN_FOIL_HAT ), array(), '1.4', true);
	}
}

$yoast_tinfoilhat = new Yoast_TinFoilHat();
$yoast_tinfoilhat->add_hooks();
