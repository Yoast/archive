=== Glue for Yoast SEO & AMP ===
Contributors: joostdevalk
Tags: AMP, SEO
Requires at least: 5.2
Tested up to: 5.4
Stable tag: 0.7
Requires PHP: 5.6.20
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Yoast SEO AMP plugin is no longer needed. Through good collaboration with Google the functionality of this plugin is now part of both Yoast SEO and the official AMP plugin.

== Description ==

The Yoast SEO AMP plugin is no longer needed. Through good collaboration with Google the functionality of this plugin is now part of both Yoast SEO and the official AMP plugin. If you still have this plugin running we’d suggest updating both the Yoast SEO and AMP plugins and removing the glue plugin.

== Changelog ==

= 0.7 =
The Yoast SEO AMP plugin is no longer needed. Through good collaboration with Google the functionality of this plugin is now part of both Yoast SEO and the official AMP plugin. If you still have this plugin running we’d suggest updating both the Yoast SEO and AMP plugins and removing the glue plugin.

= 0.6 =
* Bugfixes:
    * Fixes a bug where the saved option values weren't reflected in the form fields.

= 0.5 =
* Bugfixes:
    * Fixes a problem where the AMP icon and Default image could not be unset when Yoast SEO 9.0+ has been installed.

* Enhancements:
    * Adds compatibility with the [Plugin Dependencies](http://wordpress.org/plugins/plugin-dependencies/) plugin.
    * Improved image handling logic in schema. Props [Weston Ruter](https://github.com/westonruter).

= 0.4.3 =
* Bugfixes:
    * Fixes a fatal error in combination with `AMP for WordPress` version 0.7.0. Props [Ryan Kienstra](https://github.com/kienstra).

= 0.4.2 =
* Bugfixes:
    * Reverts the canonical removal.

= 0.4.1 =
* Bugfixes:
    * Fix styling of design tab.

= 0.4.0 =
* Bugfixes:
    * Removed page from post-type list to avoid unwanted canonical link.

* Enhancements:
    * Removed canonical feature because it is being handled by the AMP plugin.
    * Removed sanitizations which are already being done by the AMP plugin.
    * Added a check for Monster Insights analytics implementation and disables our implementation if present.
    * Added class selector implementation for AMP 0.4.x compatibility.

= 0.3.3 =
* Bugfixes:
    * Fixes bug where AMP was only activated for the first post type in the list.
    * Made sure that the function is not declared multiple times.

= 0.3.2 =
* Bugfixes:
    * Fixed underline setting that wasn't working.
    * Added screenshots to plugin page.

= 0.3.1 =
* Bugfixes:
    * Fixed bug where featured image wouldn't be used properly anymore.
    * Fixed bug where CSS in Extra CSS field could be wrongly escaped.
    * Fixed bug where wrong hook was used to `add_post_type_support`, causing integration issues.
    * Fixed bug where post type settings wouldn't save properly.
* Enhancement:
    * Added some more escaping to color picker functionality.
    * Made sure no notice is thrown on frontend when post type setting isn't available.

= 0.3 =
* Split the plugin into several classes.
* Added a settings page, found under SEO -> AMP
* This new settings page has:
    * A post types settings tab;
    * A design settings tab;
    * An analytics integration tab.
* Added sanitization functions that further clean up AMP output to make sure more pages pass validation.
* Added a default image (settable on the design tab) to use when a post has no image. This because the image in the JSON+LD output is required by Google.
* The plugin now automatically enables AMP GA tracking when GA by Yoast is enabled, but also allows you to add custom tracking.

= 0.1 =
* Initial version.
