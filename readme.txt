=== Better Core Video Embeds ===
Contributors: highrisedigital,wpmarkuk
Donate link: https://example.com/
Tags: embed, oembed, youtube, vimeo, performance, blocks
Requires at least: 6.0
Tested up to: 6.1.1
Stable tag: 1.1.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin which enhances the core embed block for Youtube, Daily Motion and Vimeo videos by not loading unnecessary scripts until they are needed.

== Description ==

This plugin provides page optimisations for pages and posts which have embedded Youtube, Vimeo or Daily Motion videos which have been added using the core embed block.

Without this plugin, when using the core embed block, when your page loads, lots of external scripts and styles are loaded from the embed service, regardless of whether a visitor actually interacts with the embedded video.

This plugin prevents these scripts and styles from loading until the user actually interacts with the video. It does this by replacing the video embed, on page load with the video thumbnail image (added on Youtube, Vimeo or Daily Motion). When a user clicks the thumbnail the embedded video, along with associated scripts and styles are loaded.

https://www.youtube.com/watch?v=k7A2kZWUb9Q

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

= 1.1.2 (18/01/2023) =
* Allow HTML allowed in a caption to be used with videos. Previously HTML was stripped from the caption.
* Ensure that any links added to embed captions are clickable.

= 1.1.1 (20/12/2022) =
* Fixed a bug where the <figcaption> element was removed when the video thumbnail was clicked.

= 1.1 (29/11/2022) =
* Updated a bug where the CSS was not loading for some users.
* Added support for Daily Motion videos
* Enqueued the stylesheet using the standard `wp_enqueue_style` function rather than using print styles
* Improved the way the thumbnail markup is output, making this more extensible and easier for developers to modify
* Added support for the embed caption to show beneath the thumbnail image
* Adds the embed provider slug as a class on the thumbnail wrapper

= 1.0 (23/11/2022) =
* Initial plugin launch added to the WP.org repository.

== Upgrade Notice ==

Updates provided via WordPress.org.