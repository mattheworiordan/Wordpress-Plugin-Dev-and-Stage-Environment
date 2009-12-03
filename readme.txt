=== Dev and Staging Environment Plugin ===
Contributors: mattman1c
Tags: developer, stage, dev
Requires at least: 2.0.2
Tested up to: 2.8.6
Stable tag: trunk

Provides developers with the ability to run separate Dev and Staging environments without having to manually change configuration settings.

== Description ==

Dev and Staging Environment Plugin provides developers with the ability to run separate Dev and Staging environments without having to manually change configuration settings both in the file system and database when migrating between environments.

== Installation ==

Download the plugin in one of the following formats:

1. Tar - http://mattheworiordan.com/projects/wp-plugins/dev-staging-environment/dev-staging-environment-0.2.1.tar
1. Zip - http://mattheworiordan.com/projects/wp-plugins/dev-staging-environment/dev-staging-environment-0.2.1.zip

Upload the Dev and Staging Environment plugin to your blog in your PRODUCTION environment.  It is imperative you install the plugin in your production environment first.  Then simply activate the plugin.

Then go into the Dev & Staging Environment Options in your WordPress Admin navigation area and set your Development and Staging environment settings such as the host name which will allow the plugin to identify the Development and Staging environments, along with any database settings.

Please ensure that your web application has both read and write privileges on the wp-config.php file in the WordPress root, as well as read and write privileges to the plugin folder.

Once you have set up all your settings, you should copy the plugin files along with the wp-config.php file to your Dev and/or Staging environments, and activate the plugin.

== Changelog ==

= 0.2.1 =

* Fixed a small bug where an empty configuration was causing a warning to be displayed in the Host field of the admin area for users with debugging on.

= 0.2 =

* Discovered that the plugin path was not guaranteed so all references to the plugin inserted into wp-config.php need to work out where the plugin has been installed.

= 0.1 =

* First release

== Meta ==

Written by Matthew O'Riordan, http://mattheworiordan.com
Feedback is welcome, please branch or comment as you wish.

1. Github - http://github.com/mattheworiordan/Wordpress-Plugin-Dev-and-Stage-Environment
1. Home - http://mattheworiordan.com/projects/wp-plugins/dev-staging-environment
1. Wordpress Plugin Home - http://wordpress.org/extend/plugins/dev-and-staging-environment/

Released under the GPL License: http://www.gnu.org/licenses/gpl.html
