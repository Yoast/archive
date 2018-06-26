=== Yoast SEO: Search Index Purge ===
Contributors: yoast, joostdevalk, tacoverdo, omarreiss, atimmer, jipmoors
Donate link: https://yoa.st/1up
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: SEO, XML sitemap, Google Search Console, Content analysis, Readability
Requires at least: 4.8
Tested up to: 4.9.6
Stable tag: 1.1.0
Requires PHP: 5.2.4

Remove attachment URLs from Google's index as fast as possible to prevent thin content penalties.

== Description ==

The purpose of this SEO plugin is to purge attachment URLs out of Google's index as fast as possible. This helps sites
that might have suffered from having too many thin content pages in the search index by removing them in the fastest
way possible.

## What does this plugin do?
This plugin makes all your attachment URLs return an HTTP 410 status. It then creates an XML sitemap with a recent last
modified date, containing all those URLs. The XML sitemap with recent post modified date makes Google spider _all_ those
URLs again. The 410 status code makes sure Google takes them out of its search results in the fastest way possible.

With a nice trick, we also show end users the actual attachment image, so the user experience is not as poor as when
they see an error page.

## Yoast SEO required
This plugin is an extension to [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/). It requires it to work.

## How long should you run this plugin?
After 6 months the attachment URLs should be gone from the search results. If you then remove the plugin, and keep
Yoast SEO's redirect setting on "Yes", you have the best long term behavior for attachment URLs: to redirect to the
actual attachment.

## Does Google condone this?
This method has been discussed with and OK'd by Google.

== Installation ==

=== From within WordPress ===

1. Visit 'Plugins > Add New'
1. Search for 'Yoast SEO: Search Index Purge'
1. Activate Yoast SEO: Search Index Purge from your Plugins page.
1. Go to "after activation" below.

=== Manually ===

1. Upload the `yoast-seo-search-index-purge` folder to the `/wp-content/plugins/` directory
1. Activate the Yoast SEO: Search Index Purge plugin through the 'Plugins' menu in WordPress
1. Go to "after activation" below.

=== After activation ===

== Frequently Asked Questions ==

You'll find answers to many of your questions on [kb.yoast.com](https://yoa.st/1va).

== Screenshots ==

1. The plugin takes over the Search Appearance &rarr; Media settings from Yoast SEO.

== Changelog ==

= 1.1 =
Release date: June 27th, 2018

Enhancements:
* Excludes attachments from the purge sitemap that were added after activating this plugin.

Bugfixes:
* Fixes a bug where the Attachement sitemap has the wrong "Last modified" date, this should be the time the plugin was activated.
* Fixes a bug where the "Redirect attachment URLs to the attachment itself?" can become "No" when saving other settings in Yoast SEO.

= 1.0 =
Release date: May 30th, 2018

Initial version.
