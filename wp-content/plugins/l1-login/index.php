<?php
/*
Plugin Name: Специальный url для входа
Plugin URI: http://skynin.xyz
Description: Демо хука
Version: 1.0
Author: skynin
Author URI: http://skynin.xyz
*/

add_action('login_head', function() {
    // https://vstarcam.ua/wp-admin?login=vstarcam

	if (isset($_REQUEST['city']) && $_REQUEST['city'] == 'kharkiv') {
		// var_dump($_REQUEST['login']);
	}
	elseif (isset($_REQUEST['redirect_to']) && strpos($_REQUEST['redirect_to'], 'city=kharkiv')) {
		// var_dump($_REQUEST['redirect_to']);
	}
	else {
		header('Location: /');
		exit();
	}
});

