<?php

//use wp to translate or just return original message
function openbook_wordpress_translate($message) {
	if (function_exists('__')) {
		return __($message);
	}
	else {
		return $message;
	}
}

//use wp to get option from database else return blank
//default handling must be in place
function openbook_wordpress_getoption($option_name) {
	if (function_exists('get_option')) {
		return trim(get_option($option_name));
	}
	else {
		return "";
	}
}

?>