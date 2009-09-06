<?php

//module contains logic specific to Open Library

//if json_decode is missing (< PHP5.2) use local json library
//included in main openbook.php but also required here
if(!function_exists('json_decode')) {
	include_once('openbook_json.php');
	function json_decode($data) {
		$json = new Services_JSON_ob();
		return( $json->decode($data) );
	}
}

//get data for one book in Open Library
class openbook_openlibrary_bookdata {

	public $bookkey='';
	public $bookdata='';

	function __construct($domain, $booknumber, $bookversion, $timeout, $proxy, $proxyport, $showerrors) {

		//map to one Open Library book key
		//if the book number is a standard Open Library book key, use it
		//else assume it is an ISBN and lookup the book key

		$obn_start = stripos($booknumber,"/b/OL");
		if (is_integer($obn_start)) {
			$bookkey = $booknumber;
		}
		else {
			$isbn = $booknumber;

			//clean ISBN
			$isbn = str_replace("-", "", $isbn); //dash - 13-digit ISBNs often have one, but not used by Open Library
			$isbn = str_replace(" ", "", $isbn); //spaces
			$isbn = str_replace("'", "", $isbn); //single quote - prevent problems with string concatenation

			//query Open Library for internal keys for the ISBN
			//use %22 for quotes, %20 for spaces -- urlencode encodes too much
			$querystring_bookkeys = "q={%22query%22:%22(isbn_10:(".$isbn.")%20OR%20isbn_13:(".$isbn."))%22}&text=true";
			$url_bookkeys = $domain . "/api/search?" . $querystring_bookkeys;

			$bookkeys = openbook_utilities_getUrlContents($url_bookkeys, $timeout, $proxy, $proxyport, OB_OPENLIBRARYDATAUNAVAILABLE_KEY_LANG, $showerrors);
			$obj = json_decode($bookkeys);
			$bookkeyresult = $obj->{'result'};

			//there can be multiple unique keys for a single ISBN
			$bookversioncount = count($bookkeyresult);
			if ($bookversion == "") $bookversion = 1; //if the user has not provided a version, use the most recent one (assumed order of recency)
			elseif ($bookversion > $bookversioncount) $bookversion = $bookversioncount; //if the user has provided too high a version, use the oldest one
			$bookversion = $bookversion - 1; //to match zero-based array

			$bookkey=$bookkeyresult[$bookversion];
		}

		$url = $domain . "/api/get?key=".$bookkey."&text=true";

		$this->bookkey = $bookkey;
		$this->bookdata = openbook_utilities_getUrlContents($url, $timeout, $proxy, $proxyport, OB_OPENLIBRARYDATAUNAVAILABLE_BOOK_LANG, $showerrors);
	}
}

function openbook_openlibrary_getBookData($domain, $booknumber, $bookversion, $timeout, $proxy, $proxyport, &$bookkey, $showerrors) {

}

//get author details from Open Library, return as custom array
function openbook_openlibrary_getAuthorsData($domain, $authors, $timeout, $proxy, $proxyport, $showerrors) { //$authors is an Open Library result object

	$author_array = array();

	if (is_array($authors)) {

		for($i=0;$i<count($authors);$i++) {

			$authorkey = $authors[$i] ->{'key'};
			$url_author = $domain . "/api/get?key=".$authorkey."&text=true";

			$authordata = openbook_utilities_getUrlContents($url_author, $timeout, $proxy, $proxyport, OB_OPENLIBRARYDATAUNAVAILABLE_AUTHOR_LANG, $showerrors);
			$obj = json_decode($authordata);
			$authorresult = $obj->{'result'};

			$authorname = $authorresult ->{'name'};
			$authorname = ucwords($authorname);
			$authorname = htmlspecialchars($authorname);

			$author_array[$i] = array('key'=>$authorkey, 'name'=>$authorname);
	  	}
	}

	return $author_array;
}

function openbook_openlibrary_extractValue($result, $elementname) {
	$value = $result ->{$elementname};
	$value = ucwords($value);
	$value = htmlspecialchars($value);
	return $value;
}

//no formatting
function openbook_openlibrary_extractValueExact($result, $elementname) {
	$value = $result ->{$elementname};
	return $value;
}

function openbook_openlibrary_extractList($result, $elementname) {
	$list = $result ->{$elementname};
	if (count($list)==0) return "";
	$list = join(',', $list);
	$list = htmlspecialchars($list);
	return $list;
}

function openbook_openlibrary_extractFirstFromList($result, $elementname) {
	$list = $result ->{$elementname};
	if (count($list)==0) return "";
	$first = $list[0];
	$first = ucwords($first);
	return $first;
}

function openbook_openlibrary_extractValueFromPair($result, $elementname) {
	$pair = $result ->{$elementname};
	$value = $pair ->{'value'};
	//$value = ucwords($value); //currently being used for first sentence, notes, etc, so not wanted
	$value = htmlspecialchars($value);
	return $value;
}

function openbook_openlibrary_geturl_book($domain, $bookkey) {
	return $domain . $bookkey;
}

function openbook_openlibrary_geturl_coverimage($coverserver, $bookkey, $coversize) {
	$olnumber_begin = stripos($bookkey,"/b/") + 3;
	$olnumber = substr($bookkey, $olnumber_begin);
	$url_coverimage = $coverserver . "/b/olid/" . $olnumber . $coversize . ".jpg"; //do not use ?default=false, returns broken image in IE
	return $url_coverimage;
}

function openbook_openlibrary_geturl_author($domain, $authorkey) {
	return $domain . $authorkey;
}

?>
