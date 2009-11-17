<?php
/**
 * @package Dev_Stage_Environment
 * @author Matthew O'Riordan
 * @version 0.1
 */
/*
Plugin Name: Dev and Staging Environment
Plugin URI: http://mattheworiordan.com
Description: This plugin lets you run your WP site on a DEV or STAGING server without having to change the database connection strings in config files or the Site URL in the database.   It will rewrite all URLs to the local DEV URL if running off localhost, and allow you to specify the local database connection settings.
Author: Matthew O'Riordan
Version: 0.1
Author URI: http://mattheworiordan.com/
*/

global $mo_install_pattern, $mo_install_wp_path;
$mo_install_pattern = "/include\\s\\('wp-content\/plugins\/dev-stage-environment\/wp-config-include.php'\\);\\s/";
$mo_install_wp_path = "wp-config.php";

function mo_devStageIncludeInstalled()
{
	global $mo_install_pattern, $mo_install_wp_path;
	$wpconfig = file_get_contents(ABSPATH.$mo_install_wp_path);
	return (preg_match ($mo_install_pattern, $wpconfig));
}

// check whenever the plugin is shown that the wp-config file includes the necessary files to change the environment settings
add_action('after_plugin_row_dev-stage-environment/dev-stage-plugin.php', 'mo_devStageInstallationCheck');
function mo_devStageInstallationCheck() 
{
	if (!mo_devStageIncludeInstalled()) { 
		if ($_REQUEST['install-devstage-plugin'] == 'true') {
			mo_devStageActivate();
			if (!mo_devStageIncludeInstalled()) mo_devStagInstallationIncorrect(); 
		} else {
			mo_devStagInstallationIncorrect(); 
		}
	}
}

function mo_devStagInstallationIncorrect() {
	$message_head = "<div class='update-message' style='background-color:#FFEBE8; border:1px solid #FF6666; text-align:left;'>";
	$top_message_head = "<div class='error' style='padding:3px; background-color:#FFEBE8; border:1px solid #FF6666; text-align:left;'>";
	$message = "The Dev Stage Environment plugin is not installed correctly.  Please ensure the web server has read and write access to the /wp-config.php file and <a href=\"" . $_SERVER['PHP_SELF'] . "?install-devstage-plugin=true\">click here to reinstall the plugin</a></div>";
	echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update">' . $top_message_head . $message . $message_head . $message . '</td></tr>';
}

// on activation, set up the include from wp-config
register_activation_hook(__FILE__, 'mo_devStageActivate');
function mo_devStageActivate()
{
	global $wpdb;
	global $mo_install_pattern, $mo_install_wp_path;
	$wpconfig = file_get_contents(ABSPATH.$mo_install_wp_path);
	
	if (!preg_match ($mo_install_pattern, $wpconfig)) {
		$wpconfig = preg_replace ("/\\/\\*+\\s+http:\\/+wordpress.org\\/\\s+\\*+\\//", "/** http://wordpress.org/   **/

// START: Dev Stage Environment Plugin
include ('wp-content/plugins/dev-stage-environment/wp-config-include.php'); 
// END: Dev Stage Environment Plugin", $wpconfig);
		file_put_contents  ( ABSPATH.$mo_install_wp_path, $wpconfig );
	}
}

// on de-activation, remove the include from wp-config and present an error if this cannot be removed
register_deactivation_hook(__FILE__, 'mo_devStageDeactivate');
function mo_devStageDeactivate()
{
	global $wpdb;
	global $mo_install_wp_path;
	$wpconfig = file_get_contents(ABSPATH.$mo_install_wp_path);
	
	$plugin_pattern = "/[\\r\\n]+\\/+ START: Dev Stage Environment Plugin[\\s\\S]+?\\/+ END: Dev Stage Environment Plugin/";
	
	if (preg_match ($plugin_pattern, $wpconfig)) {
		$wpconfig = preg_replace ($plugin_pattern, "", $wpconfig);
		try {
			file_put_contents  ( ABSPATH.$mo_install_wp_path, $wpconfig );
		} catch (Exception $e)
		{
			// don't do anything
		}
	}
	
	if (mo_devStageIncludeInstalled()) mo_devStageDeactivateUnsuccessful();
}

function mo_devStageDeactivateUnsuccessful() {
	$message_head = "<div class='update-message' style='background-color:#FFEBE8; border:1px solid #FF6666; text-align:left;'>";
	$top_message_head = "<div class='error' style='padding:3px; background-color:#FFEBE8; border:1px solid #FF6666; text-align:left;'>";
	$message = "<h3>There was an error trying to deactivate the Dev Staging Environment module. </h3>
	It has NOT been deactivated successfully.  <br />
	Please ensure the web server has read and write access to the /wp-config.php file.<br/><br />
	If this problem persists, please remove the include of this plugin manually from /wp-config.php for a clean uninstall.";
	
	echo ('<html><head><title>Plugin Error</title></head><body><table border="0" cellspacing="0"><tr class="plugin-update-tr"><td colspan="5" class="plugin-update">' . $top_message_head . $message . '</td></tr></table></body></html>');
	die();
}

add_action('admin_menu', 'mo_devStageMenu');

function mo_devStageMenu()
{
	add_options_page('Dev Stage Env, options page', 'Dev & Stage Environment', 9, basename(__FILE__), 'mo_devStageMenuOptions');
}

if (MO_DEV_STAGE_ENVIRONMENT == 'DEV' || MO_DEV_STAGE_ENVIRONMENT == 'STAGE')
{
	add_filter ( 'pre_option_siteurl', 'mo_devStage_siteurl', 0 ); // replace the siteurl getoption with our DEV/STAGE version
	function mo_devStage_siteurl ($param)
	{
		return WP_SITEURL;
	}

	remove_filter('template_redirect','redirect_canonical');  // allow browsing of site on any URL i.e. don't redirect to production
}

function mo_devStageMenuOptions()
{
	global $mo_devStageEnvironmentDEV, $mo_devStageEnvironmentSTAGE;
	
	if ($_POST['action'] == 'update')
	{
		$mo_devStageEnvironmentDEV = array (
				'HOSTS' => explode (",", str_replace (" ", "", $_POST['mo_devstage_dev_HOSTS'])),
				'DB_NAME' => $_POST['mo_devstage_dev_DB_NAME'],
				'DB_USER' => $_POST['mo_devstage_dev_DB_USER'],
				'DB_PASSWORD' => $_POST['mo_devstage_dev_DB_PASSWORD'],
				'DB_HOST' => $_POST['mo_devstage_dev_DB_HOST']
		);
		$mo_devStageEnvironmentSTAGE = array (
				'HOSTS' => explode (",", str_replace (" ", "", $_POST['mo_devstage_stage_HOSTS'])),
				'DB_NAME' => $_POST['mo_devstage_stage_DB_NAME'],
				'DB_USER' => $_POST['mo_devstage_stage_DB_USER'],
				'DB_PASSWORD' => $_POST['mo_devstage_stage_DB_PASSWORD'],
				'DB_HOST' => $_POST['mo_devstage_stage_DB_HOST']
		);
		$serialized = serialize (array ('DEV' => $mo_devStageEnvironmentDEV, 'STAGE' => $mo_devStageEnvironmentSTAGE));
		file_put_contents  ( dirname(__FILE__) . '/db-config.php', $serialized );
	}
?>
	<div class="wrap">
	<h2>Development and Staging Environment Settings</h2>
	
	Please note that these settings are NOT stored in the database and all changes are stored in local PHP configuration files.  Therefore, all changes made to the configuration must be made on the Production server only, and replicated to the Dev and Staging servers.  Alternatively, if you wish to make changes on the Dev and or Staging servers, you can replicate the changes in the file /wp-content/plugins/dev-stage-environment/db-config.php.
	
		<form method="post">
			<?php wp_nonce_field('update-options'); ?>
			
			<h3>Development Server</h3>
	
			<table class="form-table">
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_dev_HOSTS"><?php _e("Hosts"); ?>:</label></th>
					<td><input id="mo_devstage_dev_HOSTS" type="text" name="mo_devstage_dev_HOSTS" value="<?php echo htmlspecialchars(implode (",", $mo_devStageEnvironmentDEV['HOSTS'])); ?>" style="width: 300px" /></td>
					<td style="width:100%;">Separate host names with a comma such as "localhost, 127.0.0.1".  Leave this blank if no development server is needed.</td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_dev_DB_NAME"><?php _e("Database Name"); ?>:</label></th>
					<td><input id="mo_devstage_dev_DB_NAME" type="text" name="mo_devstage_dev_DB_NAME" value="<?php echo htmlspecialchars($mo_devStageEnvironmentDEV['DB_NAME']); ?>" /></td>
					<td>Leave this blank if you don't need different DEV database settings i.e. default settings will be used</td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_dev_DB_USER"><?php _e("Database User Name"); ?>:</label></th>
					<td><input id="mo_devstage_dev_DB_USER" type="text" name="mo_devstage_dev_DB_USER" value="<?php echo htmlspecialchars($mo_devStageEnvironmentDEV['DB_USER']); ?>" /></td>
					<td></td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_dev_DB_PASSWORD"><?php _e("Database Password"); ?>:</label></th>
					<td><input id="mo_devstage_dev_DB_PASSWORD" type="password" name="mo_devstage_dev_DB_PASSWORD" value="<?php echo htmlspecialchars($mo_devStageEnvironmentDEV['DB_PASSWORD']); ?>" /></td>
					<td></td>
				</tr>
				
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_dev_DB_HOST"><?php _e("Database Host"); ?>:</label></th>
					<td><input id="mo_devstage_dev_DB_HOST" type="text" name="mo_devstage_dev_DB_HOST" value="<?php echo htmlspecialchars($mo_devStageEnvironmentDEV['DB_HOST']); ?>" /></td>
					<td>Example localhost</td>
				</tr>
			</table>
			
			<h3>Staging Server</h3>
	
			<table class="form-table">
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_stage_HOSTS"><?php _e("Hosts"); ?>:</label></th>
					<td><input id="mo_devstage_stage_HOSTS" type="text" name="mo_devstage_stage_HOSTS" value="<?php echo htmlspecialchars(implode (",", $mo_devStageEnvironmentSTAGE['HOSTS'])); ?>" style="width: 300px" /></td>
					<td style="width:100%;">Separate host names with a comma such as "staging.mydomain.com, 210.123.123.1".  Leave this blank if no staging server is needed.</td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_stage_DB_NAME"><?php _e("Database Name"); ?>:</label></th>
					<td><input id="mo_devstage_stage_DB_NAME" type="text" name="mo_devstage_stage_DB_NAME" value="<?php echo htmlspecialchars($mo_devStageEnvironmentSTAGE['DB_NAME']); ?>" /></td>
					<td>Leave this blank if you don't need different Staging database settings i.e. default settings will be used</td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_stage_DB_USER"><?php _e("Database User Name"); ?>:</label></th>
					<td><input id="mo_devstage_stage_DB_USER" type="text" name="mo_devstage_stage_DB_USER" value="<?php echo htmlspecialchars($mo_devStageEnvironmentSTAGE['DB_USER']); ?>" /></td>
					<td></td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_stage_DB_PASSWORD"><?php _e("Database Password"); ?>:</label></th>
					<td><input id="mo_devstage_stage_DB_PASSWORD" type="password" name="mo_devstage_stage_DB_PASSWORD" value="<?php echo htmlspecialchars($mo_devStageEnvironmentSTAGE['DB_PASSWORD']); ?>" /></td>
					<td></td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="mo_devstage_stage_DB_HOST"><?php _e("Database Host"); ?>:</label></th>
					<td><input id="mo_devstage_stage_DB_HOST" type="text" name="mo_devstage_stage_DB_HOST" value="<?php echo htmlspecialchars($mo_devStageEnvironmentSTAGE['DB_HOST']); ?>" /></td>
					<td>Example localhost</td>
				</tr>
			</table>
	
			<input type="hidden" name="action" value="update" />
	
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php
}
?>
