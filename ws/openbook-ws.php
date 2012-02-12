<?php

//alpha OpenBook web service - just for the curious

include_once('../openbook_main.php');

openbook_webservice();

//web service implementation
//errors handled by existing openbook methods
function openbook_webservice() {

	//booknumber is the only field for the alpha
	$booknumber = isset($_GET['booknumber']) ? $_GET['booknumber'] : '1896951422'; //a default ISBN
	if (!$booknumber) $booknumber = '1896951422'; //set but blank

	$templatenumber = ""; //OpenBook will use default embedded template
	$publisherurl = "";
	$revisionnumber = "";

	$shortcode_array = array( 'booknumber' => $booknumber, 'templatenumber' => $templatenumber, 'publisherurl' => $publisherurl, 'revisionnumber' => $revisionnumber);

	$html = openbook_insertbookdata($shortcode_array, null);
	$html = htmlspecialchars($html);

	header('Content-type: text/xml');
	echo '<openbook>';
	echo $html;
	echo '</openbook>';
}

?>