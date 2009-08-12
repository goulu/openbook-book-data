<?php

//module handles all openbook html formatting
//no styling here, just html

function openbook_html_getCoverImage($coversize, $domain, $coverserver, $bookkey, $title, $firstsentencetext, $descriptiontext, $notestext) {
	
	//cover url, medium size
	$url_coverimage = openbook_openlibrary_geturl_coverimage($coverserver, $bookkey, $coversize);
	
	//'tooltip' text that shows when user hovers over cover image
	$hovertext = "";
	if ($firstsentencetext != "") $hovertext = OB_DISPLAY_FIRSTSENTENCE_LANG . $firstsentencetext;
	if ($descriptiontext != "") {
		if ($hovertext != "") $hovertext .= " ";
		$hovertext .= OB_DISPLAY_DESCRIPTION_LANG . $descriptiontext;
	}
	if ($notestext != "") {
		if ($hovertext != "") $hovertext .= " ";
		$hovertext .= OB_DISPLAY_NOTES_LANG . $notestext;
	}
	if ($hovertext == "") $hovertext = OB_DISPLAY_CLICKTOVIEWTITLEINOL_LANG;
	else $hovertext = OB_DISPLAY_CLICKTOVIEWTITLEINOL_LANG . '. ' .$hovertext;
	
	//assemble image html
	$html_image = "<img src='" . $url_coverimage . "' alt='" . $title . "' title='" . $hovertext . "' />";
	
	//wrap in link to book record in Open Library
	$url_bookpage = openbook_openlibrary_geturl_book($domain, $bookkey);
	
	$html_coverimage = "<a href='" . $url_bookpage . "' >" . $html_image . "</a>"; 

	return $html_coverimage;
}

function openbook_html_getTitle($domain, $bookkey, $titleprefix, $title, $subtitle) {

	if ($titleprefix != "") $title = $titleprefix . " " . $title;
	if ($subtitle != "") $title .= ": " . $subtitle; 
	
	$url_bookpage = openbook_openlibrary_geturl_book($domain, $bookkey);

	$html_title = "<a href='" . $url_bookpage . "' title='" . OB_DISPLAY_CLICKTOVIEWTITLEINOL_LANG . "' >" . $title . "</a>";

	return $html_title;
}

function openbook_html_getAuthors($domain, $author_array, $bystatement, $contributions) {

	$authorlinks = array();

	foreach($author_array as $author) {
		$authorkey = $author['key'];
		$authorname = $author['name'];

		$url_author = openbook_openlibrary_geturl_author($domain, $authorkey);
		$html_author =  "<a href='" . $url_author . "' title='" . OB_DISPLAY_CLICKTOVIEWAUTHORINOL_LANG . "' >" . $authorname . "</a>";
		$authorlinks[] = $html_author;
	}

	$html_authors = join(', ', $authorlinks);

	//if no author, use alternate, no author link
	if (!$html_authors) $html_authors = $bystatement;
	if (!$html_authors) $html_authors = $contributions;

	return $html_authors;
}

function openbook_html_getPublisher($publisher, $publisherurl) {

	$html_publisher = "";
	if ($publisher != '') {
		$html_publisher = $publisher;
		if ($publisherurl != '') {
			$html_publisher = "<a href='" . $publisherurl . "' title=" . OB_DISPLAY_CLICKTOVIEWPUBLISHER_LANG . "' >" . $publisher . "</a>";
		}
	}
	return 	$html_publisher;
}

function openbook_html_getPublishYear($publishdate) {

	try {
		$html_publishdate = "";

		if (strlen($publishdate)==4) $html_publishdate = $publishdate;
		else {
			preg_match("/[0-2][0-9][0-9][0-9]/", $publishdate, $matches);
			$html_publishdate = $matches[0];
		}

		return 	$html_publishdate;
	}
	catch(Exception $e) {
		return "";
	}
}

function openbook_html_getReadOnline($domain, $ocaid) {

	$readonline = "";
	if ($ocaid) {
		$url = $domain . '/details/' . $ocaid;
		$readonline = '<a href="' . $url . '" title="' . OB_DISPLAY_READONLINE_TITLE_LANG . '">' . OB_DISPLAY_READONLINE_LANG . '</a>';
	}
	return $readonline;
}

function openbook_html_getFindInLibrary($openurlresolver, $openurl, $findinlibraryphrase, $isbn, $title, $author) {

	$html_findinlibrary = "";

	if (!$openurlresolver || !$findinlibraryphrase) return ""; //if resolver or phrase is not configured this feature will be blank

	$url = $openurl;
	$html_findinlibrary = '<a href="' . $url . '" title="' . $findinlibraryphrase . '">' . $findinlibraryphrase . '</a>';

	return $html_findinlibrary;
}

function openbook_html_getFindInLibraryImage($openurlresolver, $openurl, $findinlibraryimagesrc, $findinlibraryphrase, $isbn, $title, $author) {

	$html_findinlibraryimage = "";

	if (!$openurlresolver || !$findinlibraryimagesrc) return ""; //if resolver or src is not configured this feature will be blank

	$url = $openurl;
	$html_findinlibraryimage = '<a href="' . $url . '" title="' . $findinlibraryphrase . '">' . '<img src="' . $findinlibraryimagesrc . '" alt="' . $findinlibraryphrase . '" /></a>';

	return $html_findinlibraryimage;
}

function openbook_html_getOpenUrl($openurlresolver, $title, $isbn, $authorlist, $publishplace, $publisher, $publishdate, $edition, $pages, $series) {
	
	if (!openurlresolver) return "";

	$openurl = $openurlresolver;
	$openurl .= '?url_ver=Z39.88-2004';
	$openurl .= '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook';
	$openurl .= openbook_html_getCoinsContents($title, $isbn, $authorlist, $publishplace, $publisher, $publishdate, $edition, $pages, $series);

	return $openurl;
}

//build the HTML for coins, as per http://ocoins.info/
function openbook_html_getCoins($title, $isbn, $authorlist, $publishplace, $publisher, $publishdate, $edition, $pages, $series) {

	$domain = openbook_utilities_getDomain();

	//meta values
	$coins .= '<span class="Z3988" ';
	$coins .= 'title="ctx_ver=Z39.88-2004';
	$coins .= '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook'; 
	$coins .= '&amp;rfr_id=info%3Asid%2F' . $domain . '%3AOpenBook';
	$coins .= '&amp;rft.genre=book';

	$coins .= openbook_html_getCoinsContents($title, $isbn, $authorlist, $publishplace, $publisher, $publishdate, $edition, $pages, $series);

	//end
	$coins .= '"></span>';	

	return $coins;
}

function openbook_html_getCoinsContents($title, $isbn, $authorlist, $publishplace, $publisher, $publishdate, $edition, $pages, $series) {

	$contents = "";

	//title, includes subtitle	
	$title = urlencode($title);
	if ($title != "") $contents .= '&amp;rft.btitle=' . $title;

	if ($isbn != "" && openbook_utilities_validISBN($isbn)) $contents .= "&amp;rft.isbn=" . $isbn;

	//authors
	$authors_coins = "";
	
	$authors = explode(",", $authorlist);
	$authorcount = count($authors);
	for($i=0;$i<$authorcount;$i++) {
		$author = $authors[$i]; //Open Library shows "William Shakespeare", i.e., first and lastname as one field;
		$author = urlencode($author);			
		$author_coins = '&amp;rft.au=' . $author;
		$authors_coins .= $author_coins;
	}
	if ($authors_coins != "") $contents .= $authors_coins;

	$publishplace = urlencode($publishplace);
	if ($publishplace != "") $contents .= "&amp;rft.place=" . $publishplace;

	$publisher = urlencode($publisher);
	if ($publisher != "") $contents .= "&amp;rft.pub=" . $publisher;

	$publishdate = urlencode($publishdate);
	if ($publishdate != "") $contents .= "&amp;rft.date=" . $publishdate;

	$edition = urlencode($edition);
	if ($edition != "") $contents .= "&amp;rft.edition=" . $edition;

	$pages = urlencode($pages);
	if ($pages != "") $contents .= "&amp;rft.tpages=" . $pages;

	$series = urlencode($series);
	if ($series != "") $contents .= "&amp;rft.series=" . $series;

	return $contents;
}

function openbook_html_getLinkWorldCat($isbn, $title, $author) {

	$html_worldcat = "";

	if (!$isbn && !$title) return ""; //if no isbn or title, this feature will be blank

	if ($isbn) $url = 'http://worldcat.org/isbn/' . $isbn; //isbn search
	else {
		//search by title and author -- expects spaces in these values as '+'
		$url = 'http://www.worldcat.org/search?q=ti%3A' . $title; 
		if ($author) $url .= '+au%3A' . $au;
		$url .= '&qt=advanced';
	}

	$html_worldcat = '<a href="' . $url . '" title="' . OB_DISPLAY_WORLDCAT_TITLE_LANG . '">' . OB_DISPLAY_WORLDCAT_LANG . '</a>';

	return $html_worldcat;
}

function openbook_html_getLinkLibraryThing($isbn, $title, $author) {

	$html_librarything = "";

	if (!$isbn && !$title) return ""; //if no isbn or title, this feature will be blank

	if ($isbn) $url = 'http://librarything.com/isbn/' . $isbn; //isbn search
	else {
		//search by title and author -- expects spaces in these values as '+'
		$url = 'http://www.librarything.com/search_works.php?q=' . $title; 
		if ($author) $url .= '+' . $author;
	}

	$html_librarything = '<a href="' . $url . '" title="' . OB_DISPLAY_LIBRARYTHING_TITLE_LANG . '">' . OB_DISPLAY_LIBRARYTHING_LANG . '</a>';

	return $html_librarything;
}

function openbook_html_getLinkGoogleBooks($isbn, $title, $author) {

	$html_googlebooks = "";

	if (!$isbn && !$title) return ""; //if no isbn or title, this feature will be blank

	if ($isbn) $url = 'http://books.google.com/books?as_isbn=' . $isbn; //isbn search
	else {
		//search by title and author -- expects spaces in these values as '+'
		$url = 'http://books.google.com/books?&as_vt=' . $title; 
		if ($author) $url .= '&as_auth=' . $author;
	}

	$html_googlebooks = '<a href="' . $url . '" title="' . OB_DISPLAY_GOOGLEBOOKS_TITLE_LANG . '">' . OB_DISPLAY_GOOGLEBOOKS_LANG . '</a>';

	return $html_googlebooks;
}

function openbook_html_getLinkBookFinder($isbn, $title, $author) {

	$html_bookfinder = "";

	if (!$isbn && !$title) return ""; //if no isbn or title, this feature will be blank

	if ($isbn) $url = 'http://www.bookfinder.com/search/?st=xl&ac=qr&isbn=' . $isbn; //isbn search
	else {
		//search by title and author -- expects spaces in these values as '+'
		$url = 'http://www.bookfinder.com/search/?submit=Begin+search&new_used=*&mode=basic&st=sr&ac=qr&title=' . $title; 
		if ($author) $url .= '&author=' . $author;
		//there is an available language parameter for the search
	}

	$html_bookfinder = '<a href="' . $url . '" title="' . OB_DISPLAY_BOOKFINDER_TITLE_LANG . '">' . OB_DISPLAY_BOOKFINDER_LANG . '</a>';

	return $html_bookfinder;
}

function openbook_html_setDelimiters($display) {

	//clear double dots, e.g., read online link might be blank
	$exceptions = array('[OB_DOT]  [OB_DOT]', '[OB_DOT] [OB_DOT]', '[OB_DOT][OB_DOT]');
	$display = str_replace($exceptions, '[OB_DOT]', $display);
	$display = str_replace($exceptions, '[OB_DOT]', $display); //first run is supposed to replace all, but doesn't

	$display = str_replace('[OB_DOT]', '&sdot;', $display);
	
	return $display;
}

?>
