=== Z Theme Switcher ===
Contributors: martenmoolenaar
Donate link: https://www.buymeacoffee.com/zodan
Tags: switch theme, theme development, development
Requires at least: 5.5
Tested up to: 6.8
Description: Switch temporarily and non-persistent to another (than the active) theme
Stable tag: 1.0
Author: Zodan
Author URI: https://zodan.nl
Text Domain: z-theme-switcher
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Allow (certain) users(roles) to easily switch to another theme.
Non-persistent, so regular users won't be bothered.

== Description ==

When we are developing themes, we quite often like to switch between the old (active) and the new (to develop) version of a theme. Sometimes without other people noticing.
This plugin does exactly that.


= What does it do? =

It lets users with certain roles see another (than the currently active) theme, by 
* Selecting a theme from the list of installed themes
* Selecting which from the site's available roles are permitted to switch themes
And
* Optionally, you can show a 'switch theme' (or 'switch back') button on the front-end

This plugin is under active development. Any feature requests are welcome at [plugins@zodan.nl](plugins@zodan.nl)!



== Installation ==

= Install the WP Theme Switcher from within WordPress =

1. Visit the plugins page within your dashboard and select ‘Add New’;
1. Search for ‘WP Theme Switcher’;
1. Activate the plugin from your Plugins page;
1. Go to ‘after activation’ below.

= Install manually =

1. Unzip the WP Theme Switcher zip file
2. Upload the unzipped folder to the /wp-content/plugins/ directory;
3. Activate the plugin through the ‘Plugins’ menu in WordPress;
4. Go to ‘after activation’ below.


= After activation =

1. On the Plugins page in WordPress you will see a 'settings' link below the plugin name;
2. On the WP Theme Switcher settings page:
**  Select the theme you want to be able to switch to
**  Select the roles with the permission to switch
3. Save your settings and you’re done!



== Frequently asked questions ==

= Does it work in a multisite environment? =

Yep. It does.

= The Switch theme button on the front-end is not showing, can you help? =

Hm. It could be that you are using a theme that does not call wp_footer (which is the hook it is linked to).
In that case, you can use the custom hook/action for this.
Just add the following php code (make sure it is somehow called on every page):
`<?php do_action('z_theme_switcher_show_toggle'); ?>`

= Do you have plans to improve the plugin =

We currently have on our roadmap:
* Adding translations
* Adding a custom capability (to be used next to roles, for those who want to add a custom role)
* Set the preference per user

If you have a feature suggestion, send us an email at [plugins@zodan.nl](plugins@zodan.nl).




== Changelog ==

= 1.0 =
* Very first version of this plugin
