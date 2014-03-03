Yoast License Manager
=====================

This library will take care of the following.

- Managing license related options
- Settings license key from constant
- Obfuscasting (valid) license keys
- Remote license activation / deactivation
- Checking for updates

## Usage

### Inside Plugins

For usage in plugins, instantiate the `Plugin_License_Manager` class and pass the required constructor parameters.

```
$license_manager = new Yoast_Plugin_License_Manager( 
		$api_url, 
		$item_name, 
		$file_slug, 
		$version, 
		$item_url, 
		$options_page, 
		$text_domain, 
		$author 
	);
```

You can then show a license form by simply calling the following function from your plugin settings pages.

```
$license_manager->show_license_form( );
```

A sample plugin is included, [have a look at its source](https://github.com/Yoast/License-Manager/blob/master/samples/sample-plugin.php).

### Inside Themes

For usage inside themes, instantiate the `Themes_License_Manager` class.

```
$license_manager = new Yoast_Theme_License_Manager( 
	$api_url, 
	$item_name, 
	$theme_slug, 
	$version, 
	$item_url, 
	$text_domain, 
	$author 
);
```

The class will automatically create a settings page where users can set and (de)activate their license under **Appearance**.

A sample theme `functions.php` file is included, [have a look here](https://github.com/Yoast/License-Manager/blob/master/samples/sample-theme-functions.php).

