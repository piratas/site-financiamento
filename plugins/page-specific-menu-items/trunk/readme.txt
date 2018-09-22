=== Page Specific Menu Items ===
Tags: page specific menu items, post specific menu items, menu, menu items, page wise menu, post wise menu,  wordpress menu, 
Requires at least: 3.5
Tested up to: 4.5
Stable tag: 1.6.5
License: GPLv3
Contributors: dharmapoudel

Allows user to select menu items page wise.

== Description ==

This plugin allows users to select menu items to show per page. One menu different menu items for different pages.
Also allows users choose which menu to use for cherrypicking menu items page wise. 

Please read the readme.txt file line by line before commenting. If you find any bugs/issues please report and I'll try to fix them asap.
Want me to keep updating this plugin with extra features? Rate this plugin.

> **ABOUT SUPPORT** I get messages (one on average per week) from users who are not happy that this plugin didn't work as per their requirement. Some message are like, 'your plugin has this and that issue and you should fix them for us'. There might not be bugs on this plugin; you just want to customize per your need or it might be the plugin limitation.

> If few users point out the same issue, I'll try to fix as soon as possible. But if it's something that you want to modify this plugin as per your requirement or add some custom features or want on demand support consider hiring a developer or contact me if you want me to work for you.


**How to make this plugin work?**

* Create a menu from **appearance > menus**
* Select the menu you want to use from **settings > PS MenuItems > Select Menu**
* Assign the menu to menu location from **appearance > menus**
* Check the items you want to hide from **pages > edit > Page Specific Menu Items**
* View the page and the selected items should be gone.

**How does this plugin works?**

* This plugin adds 'hide_this_item' class to selected menu items.

**Shall I install plugin first or create menu first?**

* It does not matter whether you first create menu and then install this plugin or vice versa.

**Does this plugin works on custom post type pages?**

* Yes it does.

**This plugin does not work for me?**

* It should since it works by adding class. Only case it does not work is when 'hide_this_item' rules is overriden by other syles (having low value in specificity).
* This is not actually this plugins issue.

= Recommended Plugins =

The following are recommended by the author:

* [BlankPress WordPress Cleaner](http://wordpress.org/plugins/bpwp-cleaner/) - This plugin allows you to clean up the WordPress mess. Better performance, Faster page load, Better security and Better WP experience.

* [BlankPress Theme Framework](https://github.com/dharmapoudel/blankpress) - Simple yet flexible HTML5 blank WordPress theme framework based on underscores. Use this as a base theme for your WP projects.


== Installation ==


**via Wordpress**

1. Go to the menu 'Plugins' -> 'Install' and search for 'Page Specific Menu Items'
2. Click 'install'

**Manual Installation**

1. Unzip the zip file and upload  to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu
3. Configure plugin from 'Settings > PS Menu Items'

== Screenshots ==
1. Page Specific Menu Items: plugin settings
2. Page Specific Menu Items: page specific settings

== Changelog ==

= 1.6.4 (2016-04-17) =
* Compatible with wordPress version 4.5

= 1.6.4 (2016-01-18) =
* Compatible with wordPress version 4.4.1

= 1.6.3 (2015-10-11) =
* Bug fixes

= 1.6.2 (2015-10-4) =
* Hotfix update

= 1.6.1 (2015-10-4) =
* Added option to easily select and deselect menu items
* Added option to set visibility of menu items by default
* Fixed plugin does not have a valid header issue (Thanks to DesignsCheap)
* Compatible with wordPress version 4.3.1

= 1.6 (2015-4-21) =
* Added option to easily select and deselect menu items
* Compatible with wordPress version 4.2

= 1.4.4 (2014-6-3) =
* Removed li from the plugin styles (some users are not using li for menu items)
* Moved plugin styles to footer

= 1.4.3 (2014-5-10) =
* Added more weight to the class 'hide_this_item'
* Updated faq (please read faq first) and readme file

= 1.4.2 (2014-2-4) =
* Added support for custom post types
* Added screenshots

= 1.3 (2014-2-3) =
* Fixed the warning on plugin install
* Plugin should now work smoothly

= 1.2 (2014-1-29) =
* Bug fixes and optimization

= 1.1 (2014-1-28) =
* Fixed menu selection on setting page
* Changed show to hide (now check items to hide)
* Warning and notices fixes and other changes

= 1.0 (2014-1-27) =
* Initial Release

== Frequently Asked Questions ==

= How to make this plugin work? =

* Create a menu from **appearance > menus**
* Select the menu you want to use from **settings > PS MenuItems > Select Menu**
* Assign the menu to menu location from **appearance > menus**
* Check the items you want to hide from **pages > edit > Page Specific Menu Items**
* View the page and the selected items should be gone.

= How does this plugin works? =

This plugin adds 'hide_this_item' class to selected menu items.

= Shall I install plugin first or create menu first? =

It does not matter whether you first create menu and then install this plugin or vice versa.

= Does this plugin works on custom post type pages? =

* Yes it does.

= What are its limitations? =

* Clearly this plugin works for  WordPress Pages, Default/Custom Posts .
* It does not work on WordPress archives, 404, etc for now. I'll support them as soon as I get time.