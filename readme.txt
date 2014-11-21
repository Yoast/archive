=== Comment Redirect ===
Contributors: joostdevalk
Donate link: http://yoast.com/donate/
Tags: comments, subscribe, subscriptions, community
Requires at least: 3.0
Tested up to: 4.0
stable tag: 1.1.2

Redirect commenters who just made their first comment to a page of your choice. Allows you to ask them to subscribe, like you on Facebook, etc.

== Description ==

Redirect commenters who just made their first comment to a page of your choice. On this page, you could thank them for commenting and ask them to subscribe to your blog, like you on Facebook, whatever you want!

For a great example of what you can reach with this plugin, read [this blog post](http://outspokenmedia.com/online-marketing/how-we-do-it-shock-community-building/) by Lisa Barone of Outspoken Media.

If you want to translate this plugin, the POT file is included, please send the author an email with your .po and .mo files and they'll be included in the plugin.

More info:

* [Comment Redirect plugin](https://yoast.com/wordpress/plugins/comment-redirect/).
* Read more about [WordPress SEO](https://yoast.com/articles/wordpress-seo/) so you can get the most out of this plugin.
* Check out the other [WordPress plugins](https://yoast.com/wordpress/plugins/) by the same author.

== Screenshots ==

1. Screenshot of the admin panel: only one setting!
2. Example thank you page from Yoast.com.

== Changelog ==

= 1.1.2 =

* Updated tested up to.
* Moved screenshots to assets directory.
* Updated links in readme.txt
* Fixed Screenshots tab.
* i18n
	* Added hu_HU and fi translations.

= 1.1.1 =

* Version bump.
* Added loads of languages.

= 1.1 =

* Moved from doing SQL query to using the `get_comments` function and the WP API.
* Moved the redirect function inside the class, removed all conditionals, this is faster than ever before.
* Added a filter, `yoast_comment_redirect`, to allow changing of the destination URL through a plugin and / or hooking actions.
* Made the PHPdoc actually validate and work.

= 1.0 =

* Switched to Settings API.
* Bumped version requirement to 3.0.
* Switched access to Comment Redirect settings to users with 'manage_options' capability.
* Made admin class load only when admin is currently being opened.
* Prevent an erroneous redirect when plugin has not been configured.
* Fixed a couple of potential notices.
* Added PHPDOC to the entire plugin.
* Made the plugin ready for localization.
* Added Dutch translation by the author.
* Added screenshots to the plugin page.
* Properly added the license.

= 0.1 =

* Initial release.

== Installation ==

Installation is easy:

* Download the plugin by searching for "Comment Redirect" in your WP Admin or by downloading it on the plugins page.
* If you've downloaded it, upload the file to your plugins folder.
* Activate the plugin.
* A "Comment Redirect" options panel will appear under Plugins.
* Choose the redirect page you want and Save Settings: done.
