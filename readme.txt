=== ShowOnce ===
Contributors: iltli
Donate link: http://showonce.iltli.de
Tags: ShowOnce, Alert, user alert, Front End User area, front-end user area, user area, frontend publishing, Customer Area, system messages, one time content, new user content, help notices, user notices, simple notices, notices, user messages, new user content, help tips, help tip plugin, timed content,
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Some things only need to be said once.
Use ShowOnce. Show one time messages and alerts anywhere.

== Description ==

ShowOnce is a unique plugin that allows you to display content using a shortcode which will only display once. You set the conditions, even the requirement to show until use dismisses message. You can play with it below.

ShowOnce is perfect for Wordpress site owners who have created their own front end user area, or account area. You can now use ShowOnce to show those all important new user welcome messages, status updates and more. Each with their own style.

Please read the "Other notes" to get an example list of shortcodes, or visit the plugin page for more information :-)

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= Will this change my life? =

Probably

= How does the plugin work? =

Once activated you will see the ShowOnce menu options in your admin panel. Simply add a ShowOnce entry using the ShowOnce custom post type. If you choose, you can create a custom style too, using our custom style posts also. Once are you are finished, employ the relevant shortcode to show on your front end.

= Does the plugin work for guest users? =

No, this plugin is for logged in users, of level subscribers and upwards who are logged in.

== Screenshots ==

1. Example ShowOnce Post Editor
2. Example ShowOnce Style Editor

== Changelog ==

V1.0 Ready to roll Release :-)

V1.1 Shortcode case sensitivity issue corrected.

== Upgrade notice ==

Simply delete ShowOnce plugin and upload the new version, or click update in your admin panel as new plugins become available in the Wordpress repository.

== Shortcodes ==

ShowOnce Plugin InstructionsInventory Version 1.0
Display notices and or post content once

To display custom ShowOnce post information on a page, simply add our shortcode at the location in the page you would like the ShowOnce post to display. You can see the list of options you can use with ShowOnce below:

Post= (ShowOnce custom post id, example 100, or 100,200,300)
Show= (Once or Dismiss)
style= (ShowOnce custom style id, example 12 etc)
from= (dd/mm/yy)
to= (dd/mm/yy)

[ShowOnce post=100] will dislay the contents of ShowOnce custom post id 100

This is the most basic implentation of ShowOnce. You can simply place this shortcode anywhere you like in your page or post contents and it will display the posts content of whatever ShowOnce custom post id you select.
.
[ShowOnce post=100,200,300] Will display ShowOnce custom post id contents for posts id 100,200 and 300 all at once.

[ShowOnce post=100 show=dismiss] Will display contents of ShowOnce custom post id 100 and will not dissapear until user clicks the dismiss link.

[ShowOnce post=100 show=dismiss style=2] Will display contents of ShowOnce custom post id 100 and will not dissapear until user clicks the dismiss link using the custom css style id 2 (which you will of created yourself)

[ShowOnce post=100 show=dismiss style=2 from=10/11/13] Will display contents of ShowOnce custom post id 100 and will not dissapear until user clicks the dismiss link using the custom css style id 2 (which you will of created yourself) from the date specified onwards with no stop date.

[ShowOnce post=100 show=dismiss style=2 from=10/11/13 to=12/11/13] Will display contents of ShowOnce custom post id 100 and will not dissapear until user clicks the dismiss link using the custom css style id 2 (which you will of created yourself) from the date specified onwards with no stop date.

As default you have a custom style, you can restyle the display of your ShowOnce custom posts by styling the post directly, or you can add a custom style into the ShowOnce Custom styles post type. You can then employ that style using the style=id option.

If you want premium support in making your own style then simply visit the support tab to ask your question and we can reply to you via email, providing you have given your email address to access support. Alternatively email wordpress@iltli.de