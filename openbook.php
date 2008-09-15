<?php
/*
Plugin Name: OpenBook Book Data
Plugin URI: http://johnmiedema.ca/openbook-wordpress-plugin/
Description: Displays the book cover image, title, author, and publisher from http://openlibrary.org
Version: 1.6 beta
Author: John Miedema
Author URI: http://johnmiedema.ca
=========================================================================
HISTORY

Version 1.6 beta
- can place multiple covers in the same post or page
- compatible with WordPress shortcodes
- uses new OpenLibrary cover api
- uses thumbnails only to ensure copyright compliance
- displays nicely when imported, e.g., Facebook, Bloglines

Version 1.5 beta
- new "Find in a Library" function

Version 1.4 beta
- if json_decode is missing (< PHP5.2) uses local json library from http://mike.teczno.com/JSON/JSON.phps
- many thanks to Tom Keays http://www.tomkeays.com
- note PHP5 is still required for the try/catch exception handling

Version 1.3.1 beta
- handles image sizing for IE6/IE5 too
- inline style removes border from image

Version 1.3 beta
- Added configurable timeout for curl calls b/c Open Library is down at times, default=5 sec

Version 1.2 beta
- Can use the OL number from the Open Library URL instead of ISBN
	- compensates for Open Library delay in adding new titles to their search index
	- can display titles that do not have an ISBN
- Reduced bottom padding to 10px

Version 1.1.1 beta
- Tests for JSON library when plugin is activated

Version 1.1 beta
- Replaced file_get_contents with curl because disallowed on some servers
- Replaced forced anchor target=_blank with optional argument, anchorattributes, to let user specify any anchor attributes

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

	$booknumber = "";
	$bookversion = "";
	$displayoptions = ""; //""=default, 1=cover only, 2=text only
	$publisherlink = "";
	$anchorattributes = "";
	$curltimeout = 10;
	$hidelibrary = false;

	try {

		//===================================================
		//1. Extract the arguments
		//the new shortcode format takes parameters from inside the openbook tag, extracted using shortcode_atts
		//for legacy support, this function also checks for params in the 'content', the string between tags
		//if param exists in both, legacy value is used

		extract( shortcode_atts( array(
		  'booknumber' => '',
		  'bookversion' => '',
		  'displayoptions' => '',
		  'publisherlink' => '',
		  'anchorattributes' => '',
		  'curltimeout' => 10,
		  'hidelibrary' => false
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
		$title = $bookdataresult ->{'title'};
		$subtitle = $bookdataresult ->{'subtitle'};
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
		$authors = $authorlist;
		if ($authors=="") {
		  $authors = $bookdataresult ->{'by_statement'}; //if no author, use bystatement
		  if ($authors=="") {
			 $authors=$bookdataresult ->{'contributions'}; //if no author, use contributions
			 if (is_array($authors)) $authors=implode(", ", $authors);
		  }
		}
		$authors = ucwords($authors);

		//publishers
		$publishers = $bookdataresult ->{'publishers'};
		if (is_array($publishers)) {
		  for($i=0;$i<count($publishers);$i++) {
			 $publisher = $publishers[$i];
			 if ($i==0) $publisherlist = $publisher;
				else $publisherlist = $publisherlist . ", " . $publisher;
		  }
		}
		$publishers = ucwords($publisherlist);

		//coverimage: -M gives thumbnail (used here to ensure fair use), can use -L for large,
		$olnumber_begin = stripos($bookkey,"/b/") + 3;
		$olnumber = substr($bookkey, $olnumber_begin);
		$coverimage = "http://covers.openlibrary.org/b/olid/" . $olnumber . "-M.jpg";

		//===================================================
		//4. Build the HTML
		//return blank if this isbn does not exist

		$html_bookdata = "";
		if($bookversioncount>0)
		{
			$html_coverimage = "<img src='" . $coverimage . "' alt='' border=0 style='float:left;padding-right:15px;padding-bottom:10px;' onerror=this.style.padding='0px'; />";
			$html_coverimage = "<a href='" . $bookpage . "' " . $anchorattributes . " >" . $html_coverimage . "</a>";

			//borrow -- only show for valid ISBN
			$html_borrow = "";
			if ((ereg ("([0-9]{10})", $isbn, $regs) || ereg ("([0-9]{13})", $isbn, $regs))&&($hidelibrary == false || $hidelibrary == "false"))
			{
				$html_borrow = $html_borrow . "<a href='http://worldcat.org/isbn/" . $isbn . "' " . $anchorattributes . " title='Find this title in a local library using WorldCat'>Find in a library</a>";
			}

			//title
			$html_title = "<a href='" . $bookpage . "' " . $anchorattributes . " ><i>" . $title . "</i></a>";

			//author
			$html_authors = $authors;

			//publishers
			$html_publishers = $publishers;
			if ($publisherlink != "") $html_publishers = "<a href='" . $publisherlink . "' target='_blank'>" . $publishers . "</a>";

			//assemble text
			$html_text = "<b>" . $html_title . ", " . $html_authors . "</b>; " . $html_publishers;

			//assemble
			//the div id and isbn allows for styling and dhtml handling
			$html_bookdata = "<div id=divOpenBook>";
			if ($displayoptions != 2) $html_bookdata = $html_bookdata . $html_coverimage;
			if ($displayoptions != 1) $html_bookdata = $html_bookdata . $html_text . "<br />";
			$html_bookdata = $html_bookdata . "<div>" . $html_borrow . "</div>";
			$html_bookdata = $html_bookdata . "<br></div>";
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

add_shortcode('openbook', 'openbook_insertbookdata');

?>