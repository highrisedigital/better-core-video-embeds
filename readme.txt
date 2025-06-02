=== Better Core Video Embeds ===
Contributors: highrisedigital,wpmarkuk
Donate link: https://example.com/
Tags: embed, oembed, youtube, vimeo, performance, blocks
Requires at least: 6.0
Tested up to: 6.5.4
Stable tag: 1.3.8
Requires PHP: 8.0
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

= Can I set a custom thumbnail, rather than the embed thumbnail? =

Yes! As of version 1.3, you can set a custom thumbnail on the embed block and this will be used as the video thumbnail or poster image on the front end of the site.

== Screenshots ==

1. Example of an embedded Youtube video in a post when using Better Core Embeds

== Changelog ==

= 1.3.8 (02/06/2025) =
* Remove the call to `wp_print_styles` directly before the block markup. This was adding a `<link>` element in the block with caused CSS stying issues for some. The CSS is still included in the `<head>` when the embed block is included on the page. Thanks to @markhowellsmead for reporting and helping understand this issue.

= 1.3.7 (30/10/2024) =
* Ensures that better core embeds work even when they are included in a synced pattern.
* Improves the enqueuing of the JS and CSS to be more robust.

= 1.3.6 (12/06/2024) =
* Improve the play button CSS to ensure it works with videos at aspect rations other than 16:9 and 4:3.

= 1.3.5 (28/05/2024) =
* Ensure the caption markup matches that of the original embed when the page loads.

= 1.3.4 (02/04/2024) =
* Translation of the "Play video" button text.
* A responsive video poster image if a custom (locally-hosted) image is chosen.
* Width and height parameters to remotely-hosted images.
* Thanks to Matt Radford for these updates.

= 1.3.3 (21/03/2024) =
* Ensure (previously) live video URLs from Youtube embed correctly. Thanks for @bacoords for the development on this.

= 1.3.2 (19/03/2024) =
* Use regex instead of the domDocument for grabbing a video caption.

= 1.3.1 (18/03/2024) =
* Added a fix which ensures special characters in video captions are outputted correctly.

= 1.3 (14/12/2023) =
* Add support for custom thumbnail or poster images for videos. These are upload in the block inspector.
* Caption no longer added with JS which improves developer options.
* Improved play button styling as well as making it an HTML button with an aria label for better accessibility.

= 1.2 (07/12/2023) =
* Add a name for the wrapper CSS filter which was missing.
* JS and CSS target using different classes.

= 1.1.4 (22/02/2023) =
* Load the CSS and JS on all pages. Fixes problems when post content is output on templates such as archives. Found when using full site editing and the query block.

= 1.1.3 (21/01/2023) =
* Ensure the plugin works on videos embedded from the www or no www version of a domain e.g. www.youtube.com and youtube.com.
* Output an alt attribute on the video thumbnail image.
* Correct the name of the Daily Motion thumbnail output filter.

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