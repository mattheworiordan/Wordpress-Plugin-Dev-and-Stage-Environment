<?php
	$mo_db_settings = null;
	
	global $mo_devStageEnvironmentDEV, $mo_devStageEnvironmentSTAGE;
	$mo_devStageEnvironmentDEV = $mo_devStageEnvironmentSTAGE = array('HOSTS' => array());
	try 
	{
		$dbconfigfilepath = dirname(__FILE__) . '/db-config.php';
		if ( file_exists  ( $dbconfigfilepath ) )
		{
			$devAndStageArrays = unserialize(file_get_contents(dirname(__FILE__) . '/db-config.php'));
		}
		if ($devAndStageArrays) 
		{
			$mo_devStageEnvironmentDEV = $devAndStageArrays['DEV'];
			$mo_devStageEnvironmentSTAGE = $devAndStageArrays['STAGE'];
		}
	} catch (Exception $e)
	{
		// problem unserializing
	}
	
	if (in_array($_SERVER['SERVER_NAME'],$mo_devStageEnvironmentDEV['HOSTS'])) {
		define ( 'MO_DEV_STAGE_ENVIRONMENT', 'DEV');
		if (isset ($mo_devStageEnvironmentDEV['DB_NAME']) && (trim($mo_devStageEnvironmentDEV['DB_NAME']) != "")) 
			$mo_db_settings = $mo_devStageEnvironmentDEV;
	}
		
	if (in_array($_SERVER['SERVER_NAME'],$mo_devStageEnvironmentSTAGE['HOSTS'])) {
		define ( 'MO_DEV_STAGE_ENVIRONMENT', 'STAGE');
		if (isset ($mo_devStageEnvironmentSTAGE['DB_NAME']) && (trim($mo_devStageEnvironmentSTAGE['DB_NAME']) != "")) 	
			$mo_db_settings = $mo_devStageEnvironmentSTAGE;
	}
		
	if (defined ('MO_DEV_STAGE_ENVIRONMENT'))
	{
		define('DB_NAME', $mo_db_settings['DB_NAME']);     // The name of the database
		define('DB_USER', $mo_db_settings['DB_USER']);     // Your MySQL username
		define('DB_PASSWORD', $mo_db_settings['DB_PASSWORD']); // ...and password
		define('DB_HOST', $mo_db_settings['DB_HOST']);     // ...and the server MySQL is running on
	
		$debug = 1;

		$devsiteurl = (($_SERVER['HTTPS'] != '') && ($_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

		// corresponds to WordPress address on settings page
		define('WP_SITEURL', $devsiteurl);
		// corresponds to Blog address on settings page
		define('WP_HOME', $devsiteurl);

		define ( 'WP_CONTENT_URL', $devsiteurl . '/wp-content');
		define ( 'WP_PLUGIN_URL', $devsiteurl . '/wp-content/plugins' );
	}
	
	unset ($mo_db_settings);
?>