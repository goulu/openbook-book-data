<?php
/*
Plugin Name: OpenBook
Plugin URI: http://wordpress.org/extend/plugins/openbook-book-data/
Description: Displays a book's cover image, title, author, and other book data from Open Library.
Version: 2.1.8
Author: John Miedema
Author URI: http://johnmiedema.ca/openbook-wordpress-plugin/
Support URI: http://code.google.com/p/openbook4wordpress/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('libraries/openbook_language.php'); //include before constants
include_once('libraries/openbook_constants.php');
include_once('libraries/openbook_html.php');
include_once('libraries/openbook_openlibrary.php');
include_once('libraries/openbook_utilities.php');

//handles any processing when the plugin is activated
function ob_activation_check() {

	$plugin = trim( $GET['plugin'] );

	//if json_decode is missing (< PHP5.2) use local json library
	if(!function_exists('json_decode')) {
		include_once('libraries/openbook_json.php');
		function json_decode($data) {
			$json = new Services_JSON_ob();
			return( $json->decode($data) );
		}
	}

	//test if cURL is enabled
	if (!function_exists('curl_init')) {
		deactivate_plugins($plugin);
		wp_die(OB_ENABLECURL_LANG);
	}

	//initialize options
	openbook_utilities_setDefaultOptions();
}
register_activation_hook(__FILE__, 'ob_activation_check');

//handles any cleanup when plugin is deactivated
function ob_deactivation() {
	//do not delete options at this time
	//users may want them after reactivating
	//they can use 'reset to installation' if needed
}
register_deactivation_hook(__FILE__, 'ob_deactivation');

//main function finds and replaces [openbook] shortcodes with HTML
function openbook_insertbookdata($atts, $content = null) {

	try {
		//get arguments
		$args = new openbook_arguments($atts, $content);

		$booknumber=$args->booknumber;
		$bookversion=$args->bookversion;
		$template=$args->template;
		$publisherurl=$args->publisherurl;
		$openurlresolver=$args->openurlresolver;
		$findinlibraryphrase=$args->findinlibraryphrase;
		$findinlibraryimagesrc=$args->findinlibraryimagesrc;
		$domain=$args->domain;
		$coverserver=$args->coverserver;
		$proxy=$args->proxy;
		$proxyport=$args->proxyport;
		$timeout=$args->timeout;
		$showerrors=$args->showerrors;

		//get book data
		$bdata = new openbook_openlibrary_bookdata($domain, $booknumber, $bookversion, $timeout, $proxy, $proxyport, $showerrors);

		$bookdata = $bdata->bookdata;
		$bookkey = $bdata->bookkey;

		if (!$bookdata) return openbook_getDisplayMessage(OB_NOBOOKDATAFORBOOKNUMBER_LANG);

		//extract book data values
		$obj = json_decode($bookdata);
		$bookdataresult = $obj->{'result'};

		//prepare OL data elements, prefixed with $OL_
		//corresponds to list in help_dataelements.txt, each element can be used in the WordPress options panel

		$OL_BOOK_KEY = $bookkey;

		$OL_COVER_SMALL = openbook_openlibrary_geturl_coverimage($coverserver, $bookkey, OB_OPENLIBRARY_IMGSRC_COVERSIZE1);
		$OL_COVER_MEDIUM = openbook_openlibrary_geturl_coverimage($coverserver, $bookkey, OB_OPENLIBRARY_IMGSRC_COVERSIZE2);
		$OL_COVER_LARGE = openbook_openlibrary_geturl_coverimage($coverserver, $bookkey, OB_OPENLIBRARY_IMGSRC_COVERSIZE3);

		$OL_TITLEPREFIX = openbook_openlibrary_extractValue($bookdataresult, 'title_prefix');
		$OL_TITLE = openbook_openlibrary_extractValue($bookdataresult, 'title');
		$OL_SUBTITLE = openbook_openlibrary_extractValue($bookdataresult, 'subtitle');

		$authors = $bookdataresult ->{'authors'};
		$author_array = openbook_openlibrary_getAuthorsData($domain, $authors, $timeout, $proxy, $proxyport, $showerrors);
		$authornames = array();
		foreach($author_array as $author) {
			$authornames[] = $author['name'];
		}
		$OL_AUTHORLIST = join(',', $authornames);
		$OL_AUTHORFIRST = $authornames[0];

		$OL_BYSTATEMENT = openbook_openlibrary_extractValue($bookdataresult, 'by_statement');
		$OL_CONTRIBUTIONLIST = openbook_openlibrary_extractList($bookdataresult, 'contributions');

		$OL_SERIESLIST = openbook_openlibrary_extractList($bookdataresult, 'series');
		$OL_SERIESFIRST = openbook_openlibrary_extractFirstFromList($bookdataresult, 'series');

		$OL_EDITION = openbook_openlibrary_extractValue($bookdataresult, 'edition_name');

		$OL_PUBLISHERLIST = openbook_openlibrary_extractList($bookdataresult, 'publishers');
		$OL_PUBLISHERFIRST = openbook_openlibrary_extractFirstFromList($bookdataresult, 'publishers');
		$OL_PUBLISHPLACESLIST = openbook_openlibrary_extractList($bookdataresult, 'publish_places');
		$OL_PUBLISHPLACEFIRST = openbook_openlibrary_extractFirstFromList($bookdataresult, 'publish_places');

		$OL_PUBLISHDATE = openbook_openlibrary_extractValue($bookdataresult, 'publish_date');
		$OL_COPYRIGHTDATE = openbook_openlibrary_extractValue($bookdataresult, 'copyright_date');
		$OL_PAGINATION = openbook_openlibrary_extractValue($bookdataresult, 'pagination');
		$OL_SIZE = openbook_openlibrary_extractValue($bookdataresult, 'physical_dimensions');
		$OL_PAGES = openbook_openlibrary_extractValue($bookdataresult, 'number_of_pages');
		$OL_FORMAT = openbook_openlibrary_extractValue($bookdataresult, 'physical_format');
		$OL_WEIGHT = openbook_openlibrary_extractValue($bookdataresult, 'weight');

		$OL_ISBN13LIST = openbook_openlibrary_extractList($bookdataresult, 'isbn_13');
		$OL_ISBN13FIRST = openbook_openlibrary_extractFirstFromList($bookdataresult, 'isbn_13');
		$OL_ISBN10LIST = openbook_openlibrary_extractList($bookdataresult, 'isbn_10');
		$OL_ISBN10FIRST = openbook_openlibrary_extractFirstFromList($bookdataresult, 'isbn_10');

		$isbn = "";
		if (openbook_utilities_validISBN($booknumber)) $isbn = $booknumber;
		elseif (openbook_utilities_validISBN($OL_ISBN13FIRST)) $isbn=$OL_ISBN13FIRST;
		elseif (openbook_utilities_validISBN($OL_ISBN10FIRST)) $isbn=$OL_ISBN10FIRST;
		$OL_ISBN = $isbn; //comes from parameter, or looked up in Open Library

		$OL_SUBJECTLIST = openbook_openlibrary_extractList($bookdataresult, 'subjects');
		$OL_GENRELIST = openbook_openlibrary_extractList($bookdataresult, 'genres');
		$OL_URILIST = openbook_openlibrary_extractList($bookdataresult, 'uris');
		$OL_PURCHASEURLLIST = openbook_openlibrary_extractList($bookdataresult, 'purchase_url');
		$OL_DOWNLOADURLLIST = openbook_openlibrary_extractList($bookdataresult, 'download_url');
		$OL_DESCRIPTION = openbook_openlibrary_extractValueFromPair($bookdataresult, 'description');
		$OL_FIRSTSENTENCE = openbook_openlibrary_extractValueFromPair($bookdataresult, 'first_sentence');
		$OL_NOTES = openbook_openlibrary_extractValueFromPair($bookdataresult, 'notes');

		//prepare formatted OB data elements, prefixed with $OB_
		//corresponds to list in help_dataelements.txt, each element can be used in the WordPress options panel

		$OB_COVER_SMALL = openbook_html_getCoverImage(OB_OPENLIBRARY_IMGSRC_COVERSIZE1, $domain, $coverserver, $bookkey, $OL_TITLE, $OL_FIRSTSENTENCE, $OL_DESCRIPTION, $OL_NOTES);
		$OB_COVER_MEDIUM = openbook_html_getCoverImage(OB_OPENLIBRARY_IMGSRC_COVERSIZE2, $domain, $coverserver, $bookkey, $OL_TITLE, $OL_FIRSTSENTENCE, $OL_DESCRIPTION, $OL_NOTES);
		$OB_COVER_LARGE = openbook_html_getCoverImage(OB_OPENLIBRARY_IMGSRC_COVERSIZE3, $domain, $coverserver, $bookkey, $OL_TITLE, $OL_FIRSTSENTENCE, $OL_DESCRIPTION, $OL_NOTES);

		$OB_TITLE = openbook_html_getTitle($domain, $bookkey, $OL_TITLEPREFIX, $OL_TITLE, $OL_SUBTITLE);
		$OB_AUTHORS = openbook_html_getAuthors($domain, $author_array, $bystatement, $contributions);
		$OB_PUBLISHER = openbook_html_getPublisher($OL_PUBLISHERFIRST, $publisherurl);
		$OB_PUBLISHYEAR = openbook_html_getPublishYear($OL_PUBLISHDATE);

		$ocaid = openbook_openlibrary_extractValueExact($bookdataresult, 'ocaid'); //this value is used internally only
		$OB_READONLINE = openbook_html_getReadOnline($domain, $ocaid);

		$openurl = openbook_html_getOpenUrl($openurlresolver, $OL_TITLE, $OL_ISBN, $OL_AUTHORLIST, $OL_PUBLISHPLACEFIRST, $OL_PUBLISHERFIRST, $OL_PUBLISHDATE, $OL_EDITION, $OL_PAGES, $OL_SERIESFIRST);
		$OB_LINK_FINDINLIBRARY = openbook_html_getFindInLibrary($openurlresolver, $openurl, $findinlibraryphrase, $OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);
		$OB_IMAGE_FINDINLIBRARY = openbook_html_getFindInLibraryImage($openurlresolver, $openurl, $findinlibraryimagesrc, $findinlibraryphrase, $OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);

		$OB_LINK_WORLDCAT = openbook_html_getLinkWorldCat($OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);
		$OB_LINK_LIBRARYTHING = openbook_html_getLinkLibraryThing($OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);
		$OB_LINK_GOOGLEBOOKS = openbook_html_getLinkGoogleBooks($OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);
		$OB_LINK_BOOKFINDER = openbook_html_getLinkBookFinder($OL_ISBN, $OL_TITLE, $OL_AUTHORFIRST);

		$OB_COINS = openbook_html_getCoins($OL_TITLE, $OL_ISBN, $OL_AUTHORLIST, $OL_PUBLISHPLACEFIRST, $OL_PUBLISHERFIRST, $OL_PUBLISHDATE, $OL_EDITION, $OL_PAGES, $OL_SERIESFIRST);

		//substitue elements in template
		$display = $template;

		$display = str_ireplace('[OL_BOOK_KEY]', $OL_BOOK_KEY, $display);

		$display = str_ireplace('[OL_COVER_SMALL]', $OL_COVER_SMALL, $display);
		$display = str_ireplace('[OL_COVER_MEDIUM]', $OL_COVER_MEDIUM, $display);
		$display = str_ireplace('[OL_COVER_LARGE]', $OL_COVER_LARGE, $display);

		$display = str_ireplace('[OL_TITLEPREFIX]', $OL_TITLEPREFIX, $display);
		$display = str_ireplace('[OL_TITLE]', $OL_TITLE, $display);
		$display = str_ireplace('[OL_SUBTITLE]', $OL_SUBTITLE, $display);

		$display = str_ireplace('[OL_AUTHORLIST]', $OL_AUTHORLIST, $display);
		$display = str_ireplace('[OL_AUTHORFIRST]', $OL_AUTHORFIRST, $display);
		$display = str_ireplace('[OL_BYSTATEMENT]', $OL_BYSTATEMENT, $display);
		$display = str_ireplace('[OL_CONTRIBUTIONLIST]', $OL_CONTRIBUTIONLIST, $display);

		$display = str_ireplace('[OL_SERIESLIST]', $OL_SERIESLIST, $display);
		$display = str_ireplace('[OL_SERIESFIRST]', $OL_SERIESFIRST, $display);

		$display = str_ireplace('[OL_EDITION]', $OL_EDITION, $display);

		$display = str_ireplace('[OL_PUBLISHERLIST]', $OL_PUBLISHERLIST, $display);
		$display = str_ireplace('[OL_PUBLISHERFIRST]', $OL_PUBLISHERFIRST, $display);
		$display = str_ireplace('[OL_PUBLISHPLACELIST]', $OL_PUBLISHPLACELIST, $display);
		$display = str_ireplace('[OL_PUBLISHPLACEFIRST]', $OL_PUBLISHPLACEFIRST, $display);
		$display = str_ireplace('[OL_PUBLISHDATE]', $OL_PUBLISHDATE, $display);
		$display = str_ireplace('[OL_COPYRIGHTDATE]', $OL_COPYRIGHTDATE, $display);

		$display = str_ireplace('[OL_PAGINATION]', $OL_PAGINATION, $display);
		$display = str_ireplace('[OL_SIZE]', $OL_SIZE, $display);
		$display = str_ireplace('[OL_PAGES]', $OL_PAGES, $display);
		$display = str_ireplace('[OL_FORMAT]', $OL_FORMAT, $display);
		$display = str_ireplace('[OL_WEIGHT]', $OL_WEIGHT, $display);

		$display = str_ireplace('[OL_ISBN13LIST]', $OL_ISBN13LIST, $display);
		$display = str_ireplace('[OL_ISBN13FIRST]', $OL_ISBN13FIRST, $display);
		$display = str_ireplace('[OL_ISBN10LIST]', $OL_ISBN10LIST, $display);
		$display = str_ireplace('[OL_ISBN10FIRST]', $OL_ISBN10FIRST, $display);
		$display = str_ireplace('[OL_ISBN]', $OL_ISBN, $display);

		$display = str_ireplace('[OL_SUBJECTLIST]', $OL_SUBJECTLIST, $display);
		$display = str_ireplace('[OL_GENRELIST]', $OL_GENRELIST, $display);

		$display = str_ireplace('[OL_URILIST]', $OL_URILIST, $display);
		$display = str_ireplace('[OL_PURCHASEURLLIST]', $OL_PURCHASEURLLIST, $display);
		$display = str_ireplace('[OL_DOWNLOADURLLIST]', $OL_DOWNLOADURLLIST, $display);

		$display = str_ireplace('[OL_DESCRIPTION]', $OL_DESCRIPTION, $display);
		$display = str_ireplace('[OL_FIRSTSENTENCE]', $OL_FIRSTSENTENCE, $display);
		$display = str_ireplace('[OL_NOTES]', $OL_NOTES, $display);

		$display = str_ireplace('[OB_COVER_SMALL]', $OB_COVER_SMALL, $display);
		$display = str_ireplace('[OB_COVER_MEDIUM]', $OB_COVER_MEDIUM, $display);
		$display = str_ireplace('[OB_COVER_LARGE]', $OB_COVER_LARGE, $display);

		$display = str_ireplace('[OB_TITLE]', $OB_TITLE, $display);
		$display = str_ireplace('[OB_AUTHORS]', $OB_AUTHORS, $display);
		$display = str_ireplace('[OB_PUBLISHER]', $OB_PUBLISHER, $display);
		$display = str_ireplace('[OB_PUBLISHYEAR]', $OB_PUBLISHYEAR, $display);
		$display = str_ireplace('[OB_READONLINE]', $OB_READONLINE, $display);

		$display = str_ireplace('[OB_LINK_FINDINLIBRARY]', $OB_LINK_FINDINLIBRARY, $display);
		$display = str_ireplace('[OB_IMAGE_FINDINLIBRARY]', $OB_IMAGE_FINDINLIBRARY, $display);

		$display = str_ireplace('[OB_LINK_WORLDCAT]', $OB_LINK_WORLDCAT, $display);
		$display = str_ireplace('[OB_LINK_LIBRARYTHING]', $OB_LINK_LIBRARYTHING, $display);
		$display = str_ireplace('[OB_LINK_GOOGLEBOOKS]', $OB_LINK_GOOGLEBOOKS, $display);
		$display = str_ireplace('[OB_LINK_BOOKFINDER]', $OB_LINK_BOOKFINDER, $display);
		$display = str_ireplace('[OB_COINS]', $OB_COINS, $display);

		//last substitution: delimiters
		$display = openbook_html_setDelimiters($display);
	}
	catch(Exception $e) {

		$message = $e->getMessage();
		return openbook_getDisplayMessage($message);
	}

	//===================================================
	//6. return book data

	return $display;
}

class openbook_arguments {

	public $atts='';
	public $content='';

	public $booknumber='';
	public $bookversion='';
	public $template='';
	public $publisherurl='';
	public $openurlresolver='';
	public $findinlibraryphrase='';
	public $findinlibraryimagesrc='';
	public $domain='';
	public $coverserver='';
	public $proxy='';
	public $proxyport='';
	public $timeout='';
	public $showerrors='';

	function __construct($atts, $content) {

		$this->atts = $atts;
		$this->content = $content;

		//first check for current shortcode format
		//shortcode format takes parameters from inside the tags, e.g., [openbook booknumber="1234"]
		//if both are provided, use new shortcodes
		extract( shortcode_atts( array(
			'booknumber' => '',
			'bookversion' => '',
			'templatenumber' => '',
		  	'publisherurl' => '',
		  	), $atts ) );

		//if no shortcodes, check for legacy values
		if ($booknumber == '')
		{
			//legacy version took parameters between two tags, e.g., [openbook]booknumber="1234"[/openbook]
			if ($content != null) {
				$args = explode(",", $content);
				$argcount = count($args);
				if ($argcount==0) throw new Exception(OB_BOOKNUMBERREQUIRED_LANG);

				$booknumber=$args[0];
				if ($argcount>=2) $bookversion=$args[1];
				//legacy $displaymode handled using $templatenumber below
				if ($argcount>=4) $publisherurl=$args[3];
			}
		}

		if (!$booknumber) throw new Exception(OB_BOOKNUMBERREQUIRED_LANG);
		//if bookversion missing keep it blank, will later select the most recent version

		//collect option configurations
		//use if inline value not provided above

		if (!$templatenumber) $templatenumber = OB_OPTION_TEMPLATENUMBER_1;
		if ($templatenumber == OB_OPTION_TEMPLATENUMBER_1) $template = trim(get_option(OB_OPTION_TEMPLATE1_NAME));
		elseif ($templatenumber == OB_OPTION_TEMPLATENUMBER_2) $template = trim(get_option(OB_OPTION_TEMPLATE2_NAME));
		elseif ($templatenumber == OB_OPTION_TEMPLATENUMBER_3) $template = trim(get_option(OB_OPTION_TEMPLATE3_NAME));
		else throw new Exception(OB_INVALIDTEMPLATENUMBER_LANG);
		if (!$template) throw new Exception(OB_INVALIDTEMPLATENUMBER_LANG);

		$publisherurl = trim(urlencode($publisherurl));

		$openurlresolver = trim(get_option(OB_OPTION_FINDINLIBRARY_OPENURLRESOLVER_NAME));

		$findinlibraryphrase = trim(get_option(OB_OPTION_FINDINLIBRARY_PHRASE_NAME));
		$findinlibraryimagesrc = trim(get_option(OB_OPTION_FINDINLIBRARY_IMAGESRC_NAME));

		$domain = trim(get_option(OB_OPTION_LIBRARY_DOMAIN_NAME));
		if (!$domain) throw new Exception(OB_INVALIDDOMAIN_LANG);

		$coverserver = trim(get_option(OB_OPTION_LIBRARY_COVERSERVER_NAME));
		if (!$coverserver) throw new Exception(OB_INVALIDCOVERSERVER_LANG);

		$timeout = trim(get_option(OB_OPTION_TIMEOUT_NAME));
		$proxy = trim(get_option(OB_OPTION_PROXY_NAME));
		$proxyport = trim(get_option(OB_OPTION_PROXYPORT_NAME));

		$showerrors = get_option(OB_OPTION_SHOWERRORS_NAME);

		//set return values
		$this->booknumber=$booknumber;
		$this->bookversion=$bookversion;
		$this->template=$template;
		$this->publisherurl=$publisherurl;
		$this->template=$template;
		$this->openurlresolver=$openurlresolver;
		$this->findinlibraryphrase=$findinlibraryphrase;
		$this->findinlibraryimagesrc=$findinlibraryimagesrc;
		$this->domain=$domain;
		$this->coverserver=$coverserver;
		$this->proxy=$proxy;
		$this->proxyport=$proxyport;
		$this->timeout=$timeout;
		$this->showerrors=$showerrors;
	}
}

// action function for admin hooks
function openbook_add_pages() {
    add_options_page('OpenBook', 'OpenBook', 8, 'openbook_options.php', 'openbook_options_page'); // Add a new submenu under Options:
}

// displays the page content for the options submenu
function openbook_options_page() {
	require_once('openbook_options.php');
}

add_shortcode('openbook', 'openbook_insertbookdata');
add_action('admin_menu', 'openbook_add_pages');
add_filter('widget_text', 'do_shortcode'); //allows shortcodes in widgets

?>
