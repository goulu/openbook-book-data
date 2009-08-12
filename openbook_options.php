<div class="wrap">
<?php

include_once('libraries/openbook_constants.php');
include_once('libraries/openbook_language.php');
include_once('libraries/openbook_utilities.php');

$template1 = stripslashes(get_option(OB_OPTION_TEMPLATE1_NAME));
$template2 = stripslashes(get_option(OB_OPTION_TEMPLATE2_NAME));
$template3 = stripslashes(get_option(OB_OPTION_TEMPLATE3_NAME));
$openurlresolver = get_option(OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME);
$findinlibraryphrase = get_option(OB_OPTION_FINDINLIBRARY_PHRASE_NAME);
$findinlibraryimagesrc = get_option(OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME);
$domain = get_option(OB_OPTION_LIBRARY_DOMAIN_NAME);
$coverserver = get_option(OB_OPTION_LIBRARY_COVERSERVER_NAME);
$proxy = get_option(OB_OPTION_PROXY_NAME);
$proxyport = get_option(OB_OPTION_PROXYPORT_NAME);
$timeout = get_option(OB_OPTION_TIMEOUT_NAME);
$showerrors = get_option(OB_OPTION_SHOWERRORS_NAME);

//files affected when you add an option:
//language values in openbook_language.php
//constants in open_constants.php
//add it to openbook_utilities_setDefaultOptions
//scoop it in openbook_getArguments

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$template1 = stripslashes($_POST[OB_OPTION_TEMPLATE1_NAME]);
	$template2 = stripslashes($_POST[OB_OPTION_TEMPLATE2_NAME]);
	$template3 = stripslashes($_POST[OB_OPTION_TEMPLATE3_NAME]);
	$openurlresolver = $_POST[OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME];
	$findinlibraryphrase = $_POST[OB_OPTION_FINDINLIBRARY_PHRASE_NAME];
	$findinlibraryimagesrc = $_POST[OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME];
	$domain = $_POST[OB_OPTION_LIBRARY_DOMAIN_NAME];
	$coverserver = $_POST[OB_OPTION_LIBRARY_COVERSERVER_NAME];
	$proxy = $_POST[OB_OPTION_PROXY_NAME];
	$proxyport = $_POST[OB_OPTION_PROXYPORT_NAME];
	$timeout = $_POST[OB_OPTION_TIMEOUT_NAME];
	$showerrors = $_POST[OB_OPTION_SHOWERRORS_NAME]; if ($showerrors=='on') $showerrors=OB_HTML_CHECKED_TRUE;

	if ($_REQUEST['action'] == 'save') {

		validateRequired(OB_OPTION_TEMPLATE1_LANG, $template1);
		saveOption(OB_OPTION_TEMPLATE1_NAME, $template1);

		saveOption(OB_OPTION_TEMPLATE2_NAME, $template2);
		saveOption(OB_OPTION_TEMPLATE3_NAME, $template3);

		saveOption(OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME, $openurlresolver);

		validateRequired(OB_OPTION_FINDINLIBRARY_PHRASE_LANG, $findinlibraryphrase);
		saveOption(OB_OPTION_FINDINLIBRARY_PHRASE_NAME, $findinlibraryphrase);

		saveOption(OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME, $findinlibraryimagesrc);

		validateRequired(OB_OPTION_LIBRARY_DOMAIN_LANG, $domain);
		saveOption(OB_OPTION_LIBRARY_DOMAIN_NAME, $domain);

		validateRequired(OB_OPTION_LIBRARY_COVERSERVER_LANG, $coverserver);
		saveOption(OB_OPTION_LIBRARY_COVERSERVER_NAME, $coverserver);

		saveOption(OB_OPTION_PROXY_NAME, $proxy);
		saveOption(OB_OPTION_PROXYPORT_NAME, $proxyport);

		validateRequired(OB_OPTION_TIMEOUT_LANG, $timeout);
		saveOption(OB_OPTION_TIMEOUT_NAME, $timeout);

		saveOption(OB_OPTION_SHOWERRORS_NAME, $showerrors);

		echo '<strong><em>' . OB_OPTIONS_CONFIRM_SAVED_LANG . '</strong></em>';
	}
	else if($_REQUEST['action'] == 'reset') {
			
		openbook_utilities_deleteOptions();
		openbook_utilities_setDefaultOptions();

		$template1 = get_option(OB_OPTION_TEMPLATE1_NAME);
		$template2 = get_option(OB_OPTION_TEMPLATE2_NAME);
		$template3 = get_option(OB_OPTION_TEMPLATE3_NAME);
		$openurlresolver = get_option(OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME);
		$findinlibraryphrase = get_option(OB_OPTION_FINDINLIBRARY_PHRASE_NAME);
		$findinlibraryimagesrc = get_option(OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME);
		$domain = get_option(OB_OPTION_LIBRARY_DOMAIN_NAME);
		$coverserver = get_option(OB_OPTION_LIBRARY_COVERSERVER_NAME);
		$proxy = get_option(OB_OPTION_PROXY_NAME);
		$proxyport = get_option(OB_OPTION_PROXYPORT_NAME);
		$timeout = get_option(OB_OPTION_TIMEOUT_NAME);
		$showerrors = get_option(OB_OPTION_SHOWERRORS_NAME);

		echo '<strong><em>' . OB_OPTIONS_CONFIRM_RESET_LANG  . '</strong></em>';
	}
}

function validateRequired($option_name, $option_value) {
	$option_value = trim($option_value);
	$message = $option_name . OB_VALUEREQUIRED_LANG;
	if ($option_value == '') wp_die($message);
}

//update or insert
function saveOption($option_name, $option_value) {

	$option_value = trim($option_value);

	if (get_option($option_name)) {
    		update_option($option_name, $option_value);
  	}
	else {
    		$deprecated='';
    		$autoload='no';
		delete_option($option_name); //handles case where option exists with a blank value - fails get_option test in this function
    		add_option($option_name, $option_value, $deprecated, $autoload);
  	}
}

?>

<h2>OpenBook</h2>

<form method="post" action="">

<h3><?php echo OB_OPTIONS_TEMPLATETEMPLATES_LANG; ?></h3>
<p><?php echo OB_OPTIONS_TEMPLATETEMPLATES_DETAIL_LANG; ?></p>

<table class="form-table">

<tr valign="top">
<td width="12%"><?php echo OB_OPTION_TEMPLATE1_LANG ?></td>
<td><textarea cols="80" rows="8" name="<?php echo OB_OPTION_TEMPLATE1_NAME ?>" ><?php echo $template1; ?></textarea></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTION_TEMPLATE2_LANG ?></td>
<td><textarea cols="80" rows="8" name="<?php echo OB_OPTION_TEMPLATE2_NAME ?>" ><?php echo $template2; ?></textarea></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTION_TEMPLATE3_LANG ?></td>
<td><textarea cols="80" rows="8" name="<?php echo OB_OPTION_TEMPLATE3_NAME ?>" ><?php echo $template3; ?></textarea></td>
</tr>

</table>

<h3><?php echo OB_OPTIONS_FINDINLIBRARY_LANG; ?></h3>
<table class="form-table">

<tr valign="top">
<td><?php echo OB_OPTIONS_FINDINLIBRARY_OPENURLRESOLVER_LANG; ?></td>
<td><input type="text" name="<?php echo OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME ?>" value="<?php echo $openurlresolver; ?>" size="50" /></td>
<td><?php echo OB_OPTIONS_FINDINLIBRARY_OPENURLRESOLVER_DETAIL_LANG; ?> <a href="http://www.worldcat.org/registry/institutions">WorldCat Registry</a>.</td>
</tr>

<tr valign="top">
<td width="12%"><?php echo OB_OPTIONS_FINDINLIBRARY_PHRASE_LANG; ?></td>
<td><input type="text" name="<?php echo OB_OPTION_FINDINLIBRARY_PHRASE_NAME; ?>" value="<?php echo $findinlibraryphrase; ?>" size="50" /></td>
<td><?php echo OB_OPTIONS_FINDINLIBRARY_PHRASE_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td width="12%"><?php echo OB_OPTIONS_FINDINLIBRARY_IMAGESRC_LANG; ?></td>
<td><input type="text" name="<?php echo OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME; ?>" value="<?php echo $findinlibraryimagesrc; ?>" size="50" /></td>
<td><?php echo OB_OPTIONS_FINDINLIBRARY_IMAGESRC_DETAIL_LANG; ?></td>
</tr>

</table>

<h3><?php echo OB_OPTIONS_SYSTEM_LANG; ?></h3>
<table class="form-table">

<tr valign="top">
<td width="12%"><?php echo OB_OPTIONS_LIBRARY_DOMAIN_LANG; ?></td>
<td><input type="text" name="<?php echo OB_OPTION_LIBRARY_DOMAIN_NAME ?>" value="<?php echo $domain; ?>" size="50" /></td>
<td><?php echo OB_OPTIONS_LIBRARY_DOMAIN_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTIONS_LIBRARY_COVERSERVER_LANG; ?></td>
<td><input type="text" name="<?php echo OB_OPTION_LIBRARY_COVERSERVER_NAME ?>" value="<?php echo $coverserver; ?>" size="50" /></td>
<td><?php echo OB_OPTIONS_LIBRARY_COVERSERVER_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTION_SYSTEM_PROXY_LANG; ?></td>
<td><input type=text name="<?php echo OB_OPTION_PROXY_NAME ?>" value="<?php echo $proxy; ?>" size="50" /></td>
<td><?php echo OB_OPTION_SYSTEM_PROXY_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTION_SYSTEM_PROXYPORT_LANG; ?></td>
<td><input type=text name="<?php echo OB_OPTION_PROXYPORT_NAME ?>" value="<?php echo $proxyport; ?>" size="5" /></td>
<td><?php echo OB_OPTION_SYSTEM_PROXYPORT_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTION_SYSTEM_TIMEOUT_LANG; ?></td>
<td><input type=text name="<?php echo OB_OPTION_TIMEOUT_NAME ?>" value="<?php echo $timeout; ?>" size="5" /></td>
<td><?php echo OB_OPTION_SYSTEM_TIMEOUT_DETAIL_LANG; ?></td>
</tr>

<tr valign="top">
<td><?php echo OB_OPTIONS_SHOWERRORS_LANG; ?></td>
<td><input type="checkbox" name="<?php echo OB_OPTION_SHOWERRORS_NAME; ?>" <?php echo ' ' . $showerrors . ' '; ?> /> </td>
<td><?php echo OB_OPTIONS_SHOWERRORS_DETAIL_LANG; ?></td>
</tr>

</table>

<p class="submit">
<input name="save" type="submit" class="button-primary" value="<?php echo OB_OPTIONS_SAVECHANGES_LANG ?>" />
<input type="hidden" name="action" value="save" />
</form>

<form method="post">
<input name="reset" type="submit" class="button-primary" value="<?php echo OB_OPTIONS_RESET_LANG ?>" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
<br>

</div>
