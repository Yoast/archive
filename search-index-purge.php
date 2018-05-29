<?php
/**
 * Yoast SEO: Search index purge
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Yoast SEO: Search index purge
 * Version:     1.0.0
 * Plugin URI:
 * Description:
 * Author:      Team Yoast
 * Author URI:  https://yoa.st/1uk
 * Text Domain: yoast-search-index-purge
 * Domain Path: /languages/
 * License:     GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define( 'YOAST_PURGE_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'YOAST_PURGE_FILE', __FILE__ );

require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Plugin.php';
require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Attachment_Page_Server.php';
require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Control_Yoast_SEO_Settings.php';
require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Media_Settings_Tab_Content.php';
require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Require_Yoast_SEO_Version.php';

global $yoast_purge_plugin;
$yoast_purge_plugin = new Yoast_Purge_Plugin();
$yoast_purge_plugin->add_integrations();
$yoast_purge_plugin->register_hooks();

register_activation_hook( YOAST_PURGE_FILE, array( 'Yoast_Purge_Plugin', 'activate' ) );
