<?php
/*
Plugin Name: Fact Check for YoastSEO
Description: This plugin helps you write awesome fact-checked content.
Version: 1.4
Author: Team Yoast
Author URI: https://yoast.com
License: GPL v3
*/

define('FACT_CHECK_FOR_YOASTSEO', __FILE__);

class Yoast_FactCheck
{
	public function add_hooks()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Enqueues the plugin scripts.
	 */
	public function enqueue_scripts()
	{
		// only enqueue on x-edit page.
		wp_enqueue_script('fact-check-for-yoastseo', plugins_url('js/dist/factCheck-140.min.js', FACT_CHECK_FOR_YOASTSEO ), array(), '1.4', true);
	}
}

$yoast_factcheck = new Yoast_FactCheck();
$yoast_factcheck->add_hooks();
