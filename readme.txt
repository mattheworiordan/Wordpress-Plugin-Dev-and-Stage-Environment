# Dev and Staging Environment Plugin #
Contributors: Matthew O'Riordan
Tags: developer, stage, dev
Requires at least: 2.5
Tested up to: 2.8.6

Dev and Staging Environment Plugin provides developers with the ability to run separate Dev and Staging environments without having to manually change configuration settings both in the file system and database when migrating between environments.

## Description ##

Dev and Staging Environment Plugin provides developers with the ability to run separate Dev and Staging environments without having to manually change configuration settings both in the file system and database when migrating between environments.

## Installation ##

Download the plugin in one of the following formats:

* *Tar* <http://mattheworiordan.com/projects/wp-plugins/dev-stage-environment/dev-stage-environment-0.1.tar>

* *Zip* <http://mattheworiordan.com/projects/wp-plugins/dev-stage-environment/dev-stage-environment-0.1.zip>

Upload the Dev and Staging Environment plugin to your blog in your PRODUCTION environment.  It is imperative you install the plugin in your production environment first.  Then simply activate the plugin.

Then go into the Dev & Staging Environment Options in your WordPress Admin navigation area and set your Development and Staging environment settings such as the host name which will allow the plugin to identify the Development and Staging environments, along with any database settings.

Please ensure that your web application has both read and write privileges on the wp-config.php file in the WordPress root, as well as read and write privileges to the plugin folder.

Once you have set up all your settings, you should copy the plugin files along with the wp-config.php file to your Dev and/or Staging environments, and activate the plugin.

## Changelog ##

### 0.1 ###

* First release


## Meta ##

Written by Matthew O'Riordan, <http://mattheworiordan.com>
Feedback is welcome, please branch or comment as you wish.

*Github* - <http://github.com/mattheworiordan/Wordpress-Plugin-Dev-and-Stage-Environment>

*Home* - <http://mattheworiordan.com/projects/wp-plugins/dev-stage-environment>

Released under the GPL License: <http://www.gnu.org/licenses/gpl.html>
