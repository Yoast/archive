<?php
/*
Plugin Name: Image Uploadizer
Plugin URI: https://yoast.com
Description: An Awesome Image Uploader
Version: 0.1
Author: Team Yoast
Author URI: https://yoast.com
License: GPL v3

Image Uploadizer
Copyright (C) 2014, Team Yoast - pluginsupport@yoast.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class ImageUploadizer {

	public function __construct() {
		// do stuff.
	}
}

add_action( 'plugins_loaded', create_function( '', 'new ImageUploadizer();' ) );
register_activation_hook( __FILE__, array( 'ImageUploadizer', 'plugin_activation' ) );