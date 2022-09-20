=== Simple Social Images ===
Contributors: highrisedigital, wpmarkuk, keithdevon
Tags: social sharing, social media, open graph, social images, twitter, facebook, linkedin
Requires at least: 6.0
Tested up to: 6.0.2
Stable tag: 1.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generate beautiful and branded social sharing images for posts.

== Description ==

Simple Social Images automates the creation of branded, beautiful social images for your WordPress posts. 

⚠️**Important** - _for this plugin to work, it requires a paid Simple Social Images license in order to generate the images. [Purchase a license here](https://simplesocialimages.com)_.

When you share a post, page or custom post type URL from your website on social networks, they will look for an image to display. Sometimes no image can be found. Sometimes the image is generic, irrelevant, unprofessional or just embarrasing!

Custom sharing images **increase engagement** when you, or others, share your posts online. 

To solve this, many WordPress users will create their own images and upload them to WordPress. But this can be very time consuming to produce (e.g. using Canva) and difficult to maintain consistency.

With Simple Social Images you can automate this process, getting the engagement that you want while saving time.

How it works:

1. Create your template using the Simple Social Images settings page, setting colours, fonts, logo, images and sizes
2. Create and publish a post (also works for pages and custom post types)
3. The plugin will create an image and save it to your WordPress Media Library
4. The plugin will set the og:image tag to the URL of the custom image
5. Share your post online and see your custom sharing image

The images that are generated can be customised to suit your brand. You have control over:

* Fonts
* Text sizes
* Colors
* Company logo and size
* Background images

Our simple preview tool allows you to preview what you images will look like in the plugins settings page.

== Installation ==

1. Log into WordPress
2. Go to Plugins, Add New
3. Search for Simple Social Images
4. Click Install Now, then Activate
5. Go to Settings, the Simple Social Images
6. Enter your license key for [Simple Social Images](https://simplesocialimages.com) and complete the settings to setup your images.

== Frequently Asked Questions ==

= Does this plugin require a paid license? =

Yes, the plugin requires a paid license for [Simple Social Images](https://simplesocialimages.com). This gives access to the API to generate the images for each job.

= Will all the generated images not look the same? =

No, you can select which template you want to use, and then each image that is generated can have a random background image (which you can also provide), or it will use the featured image of the job itself as the background, and will also include the job title, salary and location.

= How do I test which image is used by the social networks? =

For LinkedIn:

1. Open the LinkedIn Post Inspector
2. Enter the URL of a job from your website
3. Click ‘Inspect’

You can also test how your jobs look on [Facebook](https://developers.facebook.com/tools/debug/) and [Twitter](https://cards-dev.twitter.com/validator).

== Screenshots ==

1. Simple Social Images settings screen
2. Sample generated sharing image

== Changelog ==

= 1.0 =
* Small prep for launch on wordpress.org plugin repository
* Update readme.txt for launch

= 0.4 =
* Fix default gradient direction
* Removed margin controls in favour of X and Y axis controls
* Add template preview size toggle
* Better default settings
* UI layout improvements
* element toggles
* show/hide element settings
* use number fields instead of range sliders
* add remaining blend modes

= 0.3 =
* Removed templates to allows settings to control the output of the image.

= 0.2 =
* Improved the settings page with tooltips
* License activation now included in the plugin settings page
* Flush the sites rewrite rules on plugin activation
* Prevent registering of section settings in WordPress - they are not actually settings!
* Correct typos in meta box

= 0.1 =
* Beta release

== Upgrade Notice ==

Updates provided via WordPress.org.