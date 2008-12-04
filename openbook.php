<?php
/*
Plugin Name: OpenBook Book Data
Plugin URI: http://johnmiedema.ca/openbook-wordpress-plugin/
Description: Displays the book cover image, title, author, and publisher from http://openlibrary.org
Version: 1.7
Author: John Miedema
Author URI: http://johnmiedema.ca
=========================================================================
New Features
- inserts COinS for machine reading, e.g., Zotero
- displays first sentence, description, notes from OpenLibrary when user hovers over book cover image
- new option to use small covers (smallcover=true, shortcode format only)
- does not use default image of OpenLibrary
- new admin panel displays options
- handling to prevent ", ;" display when OpenLibrary API returns blanks for title/author/publisher
=========================================================================
*/

//if json_decode is missing (< PHP5.2) use local json library
if(!function_exists('json_decode'))
{
  include_once('JSON.php');
  function json_decode($data) {
      $json = new Services_JSON();
      return( $json->decode($data) );
  }
}

//main function finds and replaces open book tags with data from Open Library
function openbook_insertbookdata($atts, $content = null) {

	$openbookversion = "1.7 beta";

	$booknumber = "";
	$bookversion = "";
	$displayoptions = ""; //""=default, 1=cover only, 2=text only
	$publisherlink = "";
	$anchorattributes = "";
	$curltimeout = 10;
	$hidelibrary = false;
	$smallcover = false;

	try {

		//===================================================
		//1. Extract the arguments
		//the new shortcode format takes parameters from inside the openbook tag, extracted using shortcode_atts
		//for legacy support, this function also checks for params in the 'content', the string between tags
		//if param exists in both, legacy value is used
		//no new options are being added to the legacy method

		extract( shortcode_atts( array(
		  'booknumber' => '',
		  'bookversion' => '',
		  'displayoptions' => '',
		  'publisherlink' => '',
		  'anchorattributes' => '',
		  'curltimeout' => 10,
		  'hidelibrary' => false,
		  'smallcover' => false
		  ), $atts ) );

		if ($content != null) {
			$args = explode(",", $content);

			$argcount = count($args);

			$booknumber=$args[0];
			if ($argcount>=2) $bookversion=$args[1];
			if ($argcount>=3) $displayoptions=$args[2];
			//fullcover arg removed for copyright
			if ($argcount>=5) $publisherlink=$args[4];
			if ($argcount>=6) $anchorattributes=$args[5];
			if ($argcount>=7) $curltimeout=$args[6];
			if ($argcount>=8) $hidelibrary=$args[7];
		}

		//===================================================
		//2. Map to one OpenLibrary book key
		//if the book number is a standard Open Library book key, use it
		//else assume it is an ISBN and lookup the book key

		$obn_start = stripos($booknumber,"/b/OL");
		if (is_integer($obn_start)) {
			$bookkey = $booknumber;
			$bookversioncount = 1;
		}
		else {
			$isbn = $booknumber;

			//clean ISBN
			//dash - 13-digit ISBNs often have one, but not used by OpenLibrary
			//spaces
			$isbn = str_replace("-", "", $isbn);
			$isbn = str_replace(" ", "", $isbn);

			//query OpenLibrary for internal IDs that match the ISBN
			//use %22 for quotes, %20 for spaces
			$url_bookkeys = "http://openlibrary.org/api/search?q={%22query%22:%22(isbn_10:(".$isbn.")%20OR%20isbn_13:(".$isbn."))%22}&text=true";
			$bookkeys = getUrlContents($url_bookkeys, $curltimeout);
			$obj = json_decode($bookkeys);
			$bookkeyresult = $obj->{'result'};

			//there can be multiple unique keys for different versions
			//if the user has not provided a version, use the first one (assumed order of recency)
			$bookversioncount = count($bookkeyresult);
			if ($bookversion == "") $bookversion = 1;
			elseif ($bookversion > $bookversioncount) $bookversion = $bookversioncount; //set to max version
			$bookversion = $bookversion - 1; //to match zero-based array

			$bookkey = $bookkeyresult[$bookversion];
		}

		$bookpage = "http://openlibrary.org" . $bookkey;

		//===================================================
		//3. Get the book data

		$url = "http://openlibrary.org/api/get?key=".$bookkey."&text=true";
		$bookdata = getUrlContents($url, $curltimeout);

		$obj = json_decode($bookdata);
		$bookdataresult = $obj->{'result'};

		//title
		$titleprefix = $bookdataresult ->{'title_prefix'};
		$title = $bookdataresult ->{'title'};
		$subtitle = $bookdataresult ->{'subtitle'};
		if ($titleprefix != "") $title=$titleprefix . " " . $title;
		if ($subtitle != "") $title=$title.": ".$subtitle; //concatenate title and subtitle
		$title=ucwords($title);

		//authors -- handle multiple
		$authors = $bookdataresult ->{'authors'};
		if (is_array($authors)) {
		  for($i=0;$i<count($authors);$i++) {
				$authorkey = $authors[$i] ->{'key'};
				$url_author = "http://openlibrary.org/api/get?key=".$authorkey."&text=true";
				$authordata = getUrlContents($url_author, $curltimeout);
				$obj = json_decode($authordata);
				$authorresult = $obj->{'result'};
				$name = $authorresult ->{'name'};
				if ($i==0) $authorlist = $name;
				else $authorlist = $authorlist . ", " . $name;
		  }
		}
		$authors = $authorlist; //authorlist gets used by COinS function
		if ($authors=="") {
		  $authors = $bookdataresult ->{'by_statement'}; //if no author, use bystatement
		  if ($authors=="") {
			 $authors=$bookdataresult ->{'contributions'}; //if no author, use contributions
			 if (is_array($authors)) $authors=implode(", ", $authors);
		  }
		}
		$authors = ucwords($authors);

		//publisher - if multiple, use the first one
		$publishers = $bookdataresult ->{'publishers'};
		if (is_array($publishers)) {
			if (count($publishers)>0) $publisher = $publishers[0];
			else $publisher = "";
		}
		$publisher = ucwords($publisher);

		//publish place - if multiple, use the first one
		$publishplaces = $bookdataresult ->{'publish_places'};
		if (is_array($publishplaces)) {
			if (count($publishplaces)>0) $publishplace = $publishplaces[0];
			else $publishplace = "";
		}
		$publishplace = ucwords($publishplace);

		//publish date
		$publishdate = $bookdataresult ->{'publish_date'};

		//coverimage:
		//-M gives thumbnail, default
		//-S gives even smaller cover
		//-L gives large cover, not used here to ensure fair use
		//default=false: disable using the default image when no image is found

		$coversize = "-S";
		if ($smallcover == false || $smallcover == "false") $coversize = "-M";

		$olnumber_begin = stripos($bookkey,"/b/") + 3;
		$olnumber = substr($bookkey, $olnumber_begin);
		$coverimage = "http://covers.openlibrary.org/b/olid/" . $olnumber . $coversize . ".jpg?default=false";

		//descriptive data
		//handle special characters so they do not break the HTMLL

		$description = $bookdataresult ->{'description'};
		$descriptiontext = $description ->{'value'};
		$descriptiontext = str_replace("'", "&#39;", $descriptiontext); //single quote

		$firstsentence = $bookdataresult ->{'first_sentence'};
		$firstsentencetext = $firstsentence ->{'value'};
		$firstsentencetext = str_replace("'", "&#39;", $firstsentencetext);

		$notes = $bookdataresult ->{'notes'};
		$notestext = $notes ->{'value'};
		$notestext = str_replace("'", "&#39;", $notestext);

		//===================================================
		//4. Build the HTML
		//return blank if this isbn does not exist

		$html_bookdata = "";
		if($bookversioncount>0)
		{
			//'tooltip' text that shows when user hovers over cover image
			$hovertext = "";
			if ($firstsentencetext != "") $hovertext = "First Sentence: " . $firstsentencetext;
			if ($descriptiontext != "")
			{
				if ($hovertext != "") $hovertext = $hovertext . " ";
				$hovertext = $hovertext . "Description: " . $descriptiontext;
			}
			if ($notestext != "")
			{
				if ($hovertext != "") $hovertext = $hovertext . " ";
				$hovertext = $hovertext . "Notes: " . $notestext;
			}

			$html_coverimage = "<img src='" . $coverimage . "' alt='' title='" . $hovertext . "' border=0 style='float:left;padding-right:15px;padding-bottom:10px;' onerror=this.style.padding='0px'; />";
			$html_coverimage = "<a href='" . $bookpage . "' " . $anchorattributes . " >" . $html_coverimage . "</a>";

			//borrow -- only show for valid ISBN
			$html_borrow = "";
			if (validISBN($isbn)&&($hidelibrary == false || $hidelibrary == "false"))
			{
				$html_borrow = $html_borrow . "<a href='http://worldcat.org/isbn/" . $isbn . "' " . $anchorattributes . " title='Find this title in a local library using WorldCat'>Find in a library</a>";
			}

			//title
			$html_title = "<a href='" . $bookpage . "' " . $anchorattributes . " ><i>" . $title . "</i></a>";

			//author
			$html_authors = $authors;

			//publisher
			$html_publisher = $publisher;
			if ($publisherlink != "") $html_publisher = "<a href='" . $publisherlink . "' target='_blank'>" . $publisher . "</a>";

			//assemble text
			//sometimes an API call returns a blank for a value, conditional logic prevents display of punctuation by itself
			$html_text = "";
			if ($title != "")
			{
				$html_text = $html_text . "<b>" . $html_title . "</b>";
			}
			if ($authors != "")
			{
				if ($html_text != "") $html_text = $html_text . "<b>, " . $html_authors . "</b>";
				else $html_text = "<b>" . $html_authors . "</b>";	 
			}
			if ($publisher != "")
			{
				if ($html_text != "") $html_text = $html_text . "; " . $html_publisher;
				else $html_text = $html_publisher;
			}
			if ($html_text == "") $html_text = "<i>Text temporarily unavailable</i>";
			
			//assemble
			$html_bookdata = "<div id=divOpenBook version='" . $openbookversion . "'>";
			if ($displayoptions != 2) $html_bookdata = $html_bookdata . $html_coverimage;
			if ($displayoptions != 1) $html_bookdata = $html_bookdata . $html_text . "<br />";
			$html_bookdata = $html_bookdata . "<div>" . $html_borrow . "</div>";
			if ($smallcover == false || $smallcover == "false") $html_bookdata = $html_bookdata . "<br />";
			$html_bookdata = $html_bookdata . "</div>";

			//add coins HTML
			$html_bookdata = $html_bookdata . buildCOinS($title, $isbn, $authorlist, $publisher, $publishplace, $publishdate);
		}
	}
	catch(Exception $e)
	{
		$message = "<i>[" . $e->getMessage() . "]</i> ";
		return $message;
	}

	//===================================================
	//6. return book data

	return $html_bookdata;
}

//this method replaces file_get_contents, which is sometimes disallowed on servers
function getUrlContents($url, $curltimeout) {

	// Establish a cURL handle.
	$ch = curl_init($url);

	// Set our options
	curl_setopt($ch, CURLOPT_HEADER, false); //false=do not include headers
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //true=return as string
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $curltimeout); //timeout for when OL is down
	curl_setopt($ch, CURLOPT_TIMEOUT, $curltimeout); //timeout for when OL is down

	// Execute the request
	$output = curl_exec($ch);

	// Close the cURL session.
	curl_close($ch);

	if ($output == "") throw new Exception("Open Library Data Unavailable");

	return $output;
}

//utility for testing if 10 or 13 digits ISBN
function validISBN($testisbn)
{
	return (ereg ("([0-9]{10})", $testisbn, $regs) || ereg ("([0-9]{13})", $testisbn, $regs));
}

//build the HTML for coins, as per http://ocoins.info/
function buildCOinS($title, $isbn, $authorlist, $publisher, $publishplace, $publishdate) {

	try
	{
		//title, includes subtitle	
		$title = urlencode($title);
		$publisher = urlencode($publisher);
		$publishplace = urlencode($publishplace);
		$publishdate = urlencode($publishdate);

		//authors
		$authors_coins = "";
		
		$authors = explode(",", $authorlist);
		$authorcount = count($authors);
		for($i=0;$i<$authorcount;$i++) {
			$author = $authors[$i]; //Open Library shows "William Shakespeare";
			$author = urlencode($author);			
			$author_coins = '&amp;rft.au=' . $author;
			$authors_coins .= $author_coins;
		}

		//assemble coins
		$coins = "";

		//constants
		$coins .= '<span class="Z3988" ';
		$coins .= 'title="ctx_ver=Z39.88-2004';
		$coins .= '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook'; 
		$coins .= '&amp;rfr_id=info%3Asid%2Fjohnmiedema.ca%3AOpenBook';
		$coins .= '&amp;rft.genre=book';

		//conditional attributes
		if ($title != "") $coins .= '&amp;rft.btitle=' . $title;
		if ($isbn != "" && validISBN($isbn)) $coins .= "&amp;rft.isbn=" . $isbn;
		if ($authors_coins != "") $coins .= $authors_coins;
		if ($publisher != "") $coins .= "&amp;rft.pub=" . $publisher;
		if ($publishplace != "") $coins .= "&amp;rft.place=" . $publishplace;
		if ($publishdate != "") $coins .= "&amp;rft.date=" . $publishdate;

		//required end
		$coins .= '"></span>';

		return $coins;
	}
	catch(Exception $e)
	{
		return "";
	}
}

// action function for admin hooks
function openbook_add_pages() {
    add_options_page('OpenBook', 'OpenBook', 8, 'openbook_admin.php', 'openbook_admin_page'); // Add a new submenu under Options:
}

// displays the page content for the Admin submenu
function openbook_admin_page() {
	require_once('openbook_admin.php');
}

add_shortcode('openbook', 'openbook_insertbookdata');
add_action('admin_menu', 'openbook_add_pages');

?>
