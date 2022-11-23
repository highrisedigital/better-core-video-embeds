=== Better Core Video Embeds ===
Contributors: highrisedigital,wpmarkuk
Donate link: https://example.com/
Tags: embed, oembed, youtube, vimeo
Requires at least: 6.0
Tested up to: 6.1.1
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin which enhances the core embed block for Youtube and Vimeo videos by not loading unnecessary scripts until they are needed.

== Description ==

This plugin provides page optimisations for pages and posts which have embedded Youtube or Vimeo videos which have been added using the core embed block.

Without this plugin, when using the core embed block, when your page loads, lots of Youtube and/or Vimeo scripts and styles are loaded, regardless of whether a visitor actually interacts with the embedded video.

This plugin prevents these scripts and styles from loading until the user actually interacts with the video. It does this by replacing the video embed, on page load with the video thumbnail image (added on Youtube or Vimeo). When a user clicks the thumbnail the embedded video, along with associated scripts and styles in then loaded.

== Installation ==

1. Log into WordPress
2. Go to Plugins, Add New
3. Search for Better Core Video Embeds
4. Click Install Now, then Activate
6. Embed a Youtube and Vimeo videos into your posts as you would normally using an embed block, or just pasting the video URL into the editor.

== Frequently Asked Questions ==

= What does the plugin actually do? =

It filters the embed block output to prevent Youtube and Vimeo scripts be loaded on the page until a users clicks to watch a video. It does this my replacing the normal embed markup with a video thumbnail image. When the user clicks on the image, the embedded video is called.

= What happens to my videos if I stop using this plugin? =

The plugin provides progressive enhancement, therefore if you no longer use this plugin, your embedded videos will revert back to the default WordPress behaviour.

== Screenshots ==

1. Example of an embedded Youtube video in a post when using Better Core Embeds

== Changelog ==

= 1.0 (23/11/2022) =
* Initial plugin launch

== Upgrade Notice ==

Updates provided via WordPress.org.