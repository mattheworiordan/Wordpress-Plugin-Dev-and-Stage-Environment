<?php
	include ('../includes/markdown.php');
	echo (Markdown(file_get_contents(dirname(__FILE__) . '/readme.txt')));
?>