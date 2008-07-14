<?php
/*
Plugin Name: OpenBook Book Data
Plugin URI: http://johnmiedema.ca/openbook-wordpress-plugin/
Description: Displays the book cover image, title, author, and publisher from http://openlibrary.org
Version: 1.3 beta
Author: John Miedema
Author URI: http://johnmiedema.ca
=========================================================================
HISTORY

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

function openbook_insertbookdata($content) {

	$booknumber = "";
	$bookversion = "";
	$displayoptions = ""; //""=default, 1=cover only, 2=text only
	$fullcover = false;
	$publisherlink = "";
	$anchorattributes = "";
	$curltimeout = 10;

	try {
		//check if the openbook tags occur in the post, if not, do nothing

		$opentagstart = stripos($content,"[openbook]");
		$closetagstart = stripos($content,"[/openbook]");

		if ($opentagstart != "" && $closetagstart != "") {

			//===================================================
			//1. Extract the arguments

			$opentagend = $opentagstart + 9;
			$args_start = $opentagend + 1;
			$args_length = ($closetagstart - $args_start);
			$args = explode(",", substr($content, $args_start, $args_length));

			$argcount = count($args);

			$booknumber=$args[0];
			if ($argcount>=2) $bookversion=$args[1];
			if ($argcount>=3) $displayoptions=$args[2];
			if ($argcount>=4) $fullcover=$args[3];
			if ($argcount>=5) $publisherlink=$args[4];
			if ($argcount>=6) $anchorattributes=$args[5];
			if ($argcount>=7) $curltimeout=$args[6];

			$tagstringlength = ($closetagstart + 11) - $opentagstart;
			$tagstring = substr($content, $opentagstart, $tagstringlength);

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

			//coverimage

			//there seems to be a standard URL format for most cover images
			//Open Library converts gif to jpg
			$isbn10=$bookdataresult ->{'isbn_10'};
			if ($isbn10 != "") {
			  $isbn10 = $isbn10[0];
			  $coverimage = "http://openlibrary.org/static/bookcovers/full/" . substr($isbn10, 0, 1) . "/" . substr($isbn10, 1, 1) . "/" . $isbn10 . ".jpg";
			}

			//a coverimage is returned sometimes, if it exists use it
			$coveralternate= $bookdataresult ->{'coverimage'};
			if ($coveralternate != "") {
			  $coveralternate = str_replace("\\", "", $coveralternate);
			  $coveralternate = "http://openlibrary.org" . $coveralternate;
			  $coverimage=$coveralternate;
			}

			//===================================================
			//4. Build the HTML
			//return blank if this isbn does not exist

			$html_bookdata = "";
			if($bookversioncount>0)
			{
				//coverimage
				$html_size = "";
				if ($fullcover == false || $fullcover == "false") $html_size = "max-width:150px;max-height:225px;";

				$html_coverimage = "<img src='" . $coverimage . "' alt='' style='float:left;padding-right:15px;padding-bottom:10px;" . $html_size . "' onerror=this.style.padding='0px'; />";

				$html_coverimage = "<a href='" . $bookpage . "' " . $anchorattributes . " >" . $html_coverimage . "</a>";

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
				$html_bookdata = "<div id=divOpenBook isbn='" . $isbn . "'>";
				if ($displayoptions != 2) $html_bookdata = $html_bookdata . $html_coverimage;
				if ($displayoptions != 1) $html_bookdata = $html_bookdata . $html_text . "<br />";
				$html_bookdata = $html_bookdata . "</div>";
			}

			//===================================================
			//5. strip out openbook tags

			$content = str_replace($tagstring, "", $content);
		}
	}
	catch(Exception $e)
	{
		$message = "<i>[" . $e->getMessage() . "]</i> ";

		//place message in openbook tags
		$content = str_replace($tagstring, $message, $content);
	}

	//===================================================
	//6. replace content

	//insert book data at the beginning of the content
	$content = $html_bookdata . $content;

	echo $content;
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

//PHP5 is required for the JSON libraries
function openbook_activation_check(){

	if(!function_exists('json_decode') ) {

    	deactivate_plugins(basename('openbook.php')); //deactivate OpenBook
     	wp_die("Sorry, but you cannot run this plugin, as the JSON libraries were not found.");
	}
}

register_activation_hook('openbook.php', 'openbook_activation_check');

add_filter('the_content', 'openbook_insertbookdata');

?>