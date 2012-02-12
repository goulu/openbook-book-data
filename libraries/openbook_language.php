<?php

//errors

define('OB_ENABLECURL_LANG', openbook_wordpress_translate('OpenBook uses the PHP cURL library. Ask your system administrator to enable this package.'));
define('OB_BOOKNUMBERREQUIRED_LANG', openbook_wordpress_translate('OpenBook requires at least a book number, e.g., ISBN or Open Library key'));
define('OB_INVALIDTEMPLATENUMBER_LANG', openbook_wordpress_translate('Invalid template or template number. Correct the template, or enter a template number of 1-5. If this does not work, click \'Reset to installation values\' in Settings.'));

define('OB_VALUEREQUIRED_LANG', openbook_wordpress_translate(' is a required value. Please return to the OpenBook settings and enter a value.'));

define('OB_OPENLIBRARYDATAUNAVAILABLE_KEY_LANG', openbook_wordpress_translate('Open Library Data Unavailable')); //most common
define('OB_OPENLIBRARYDATAUNAVAILABLE_BOOK_LANG', openbook_wordpress_translate('Open Library Data Unavailable (books)'));
define('OB_OPENLIBRARYDATAUNAVAILABLE_AUTHOR_LANG', openbook_wordpress_translate('Open Library Data Unavailable (authors)'));

define('OB_NOBOOKDATAFORBOOKNUMBER_LANG', openbook_wordpress_translate('No Book Data for this Book Number'));
define('OB_INVALIDDOMAIN_LANG', openbook_wordpress_translate('Invalid domain. The usual value is http://openlibrary.org.'));
define('OB_INVALIDCOVERSERVER_LANG', openbook_wordpress_translate('Invalid cover server. The usual value is http://covers.openlibrary.org.'));

define('OB_CURLTIMEOUT_LANG', openbook_wordpress_translate('Timeout contacting Open Library'));
define('OB_CURLERROR_LANG', openbook_wordpress_translate('Error contacting Open Library'));
define('OB_OLSERVERERROR_LANG', openbook_wordpress_translate('Open Library Server Error'));

//options page

define('OB_OPTIONS_TEMPLATETEMPLATES_LANG', openbook_wordpress_translate('Templates'));
define('OB_OPTIONS_TEMPLATETEMPLATES_DETAIL_LANG', openbook_wordpress_translate('Modify these templates to change the content and order of the OpenBook display elements. Template 1 is the default, but you can change the template number in the Visual Editor dropdown or in a shortcode, e.g., [openbook booknumber="123" templatenumber="2"]. Modify the template styles by editing the OpenBook stylesheet found in the plugin folder. For more information visit the '));

define('OB_OPTION_TEMPLATE1_LANG', openbook_wordpress_translate('Template 1 (default)'));
define('OB_OPTION_TEMPLATE2_LANG', openbook_wordpress_translate('Template 2 (e.g., smaller cover for widgets)'));
define('OB_OPTION_TEMPLATE3_LANG', openbook_wordpress_translate('Template 3 (e.g., large cover)'));
define('OB_OPTION_TEMPLATE4_LANG', openbook_wordpress_translate('Template 4 (e.g., inline text link)'));
define('OB_OPTION_TEMPLATE5_LANG', openbook_wordpress_translate('Template 5 (e.g., academic reference)'));

define('OB_OPTIONS_FINDINLIBRARY_LANG', openbook_wordpress_translate('Find in the Library'));

define('OB_OPTIONS_FINDINLIBRARY_OPENURLRESOLVER_LANG', openbook_wordpress_translate('OpenURL Resolver'));
define('OB_OPTIONS_FINDINLIBRARY_OPENURLRESOLVER_DETAIL_LANG', openbook_wordpress_translate("If you enter a library's OpenURL resolver (version 1.0) here, and add [OB_LINK_FINDINLIBRARY] or [OB_IMAGE_FINDINLIBRARY] to a template, a link will point to that library's records. To find the resolver, ask the Systems Librarian or look it up in the "));

define('OB_OPTIONS_FINDINLIBRARY_PHRASE_LANG', openbook_wordpress_translate('Phrase'));
define('OB_OPTIONS_FINDINLIBRARY_PHRASE_DETAIL_LANG', openbook_wordpress_translate('If you enter an OpenURL resolver, and add [OB_LINK_FINDINLIBRARY] to a template, this phrase is used for the text link. You may wish to name your library.'));

define('OB_OPTIONS_FINDINLIBRARY_IMAGESRC_LANG', openbook_wordpress_translate('Image Source'));
define('OB_OPTIONS_FINDINLIBRARY_IMAGESRC_DETAIL_LANG', openbook_wordpress_translate('If you enter an OpenURL resolver, and add [OB_IMAGE_FINDINLIBRARY] to a template, this image URL is used for the image link. You may wish to use your library\'s image.'));

define('OB_OPTIONS_SYSTEM_LANG', openbook_wordpress_translate('System'));

define('OB_OPTIONS_LIBRARY_DOMAIN_LANG', openbook_wordpress_translate('Library Domain'));
define('OB_OPTIONS_LIBRARY_COVERSERVER_LANG', openbook_wordpress_translate('Cover Server'));
define('OB_OPTION_SYSTEM_TIMEOUT_LANG', openbook_wordpress_translate('Timeout (sec)'));
define('OB_OPTION_SYSTEM_PROXY_LANG', openbook_wordpress_translate('Proxy'));
define('OB_OPTION_SYSTEM_PROXYPORT_LANG', openbook_wordpress_translate('Proxy Port'));

define('OB_OPTIONS_LIBRARY_DOMAIN_DETAIL_LANG', openbook_wordpress_translate('Use the default value for Open Library or enter the domain of your local installation'));
define('OB_OPTION_SYSTEM_TIMEOUT_DETAIL_LANG', openbook_wordpress_translate('The timeout for connecting with Open Library. Increase to wait longer. Decrease if page loads are hanging.'));
define('OB_OPTION_SYSTEM_PROXY_DETAIL_LANG', openbook_wordpress_translate('May be needed if you are behind a firewall. Ask your system administrator for this value and the port.'));
define('OB_OPTION_SYSTEM_PROXYPORT_DETAIL_LANG', openbook_wordpress_translate('Goes with the proxy. Just enter the number, no colon.'));

define('OB_OPTIONS_SHOWERRORS_LANG', openbook_wordpress_translate('Show Error Details'));
define('OB_OPTIONS_SHOWERRORS_DETAIL_LANG', openbook_wordpress_translate('If checked, OpenBook displays detailed information if an error occurs. Useful for diagnosing problems.'));

define('OB_OPTIONS_SAVETEMPLATES_LANG', openbook_wordpress_translate('Save Settings'));
define('OB_OPTIONS_SAVETEMPLATES_DETAIL_LANG', openbook_wordpress_translate('If checked, OpenBook will save your settings when the plugin is deactivated, otherwise it will delete them.'));

define('OB_OPTIONS_SAVECHANGES_LANG', openbook_wordpress_translate('Save Changes'));
define('OB_OPTIONS_RESET_LANG', openbook_wordpress_translate('Reset to Installation Values'));

define('OB_OPTIONS_CONFIRM_SAVED_LANG', openbook_wordpress_translate('Your changes have been saved'));
define('OB_OPTIONS_CONFIRM_RESET_LANG', openbook_wordpress_translate('The options have been reset to the original installation values'));

//display

define('OB_DISPLAY_FIRSTSENTENCE_LANG', openbook_wordpress_translate('First Sentence: '));
define('OB_DISPLAY_DESCRIPTION_LANG', openbook_wordpress_translate('Description: '));
define('OB_DISPLAY_NOTES_LANG', openbook_wordpress_translate('Notes: '));
define('OB_DISPLAY_CLICKTOVIEWTITLEINOL_LANG', openbook_wordpress_translate('View this title in Open Library'));
define('OB_DISPLAY_CLICKTOVIEWAUTHORINOL_LANG', openbook_wordpress_translate('View this author in Open Library'));
define('OB_DISPLAY_CLICKTOVIEWPUBLISHER_LANG', openbook_wordpress_translate('View the publisher\'s website'));
define('OB_DISPLAY_FINDINLIBRARY_WORLDCAT_TITLE_LANG', openbook_wordpress_translate('Find this title in a library using WorldCat'));
define('OB_DISPLAY_FINDINLIBRARY_OPENURL_TITLE_LANG', openbook_wordpress_translate('Find this title in the library'));
define('OB_DISPLAY_READONLINE_LANG', openbook_wordpress_translate('Read Online'));
define('OB_DISPLAY_READONLINE_TITLE_LANG', openbook_wordpress_translate('Read this work online'));

define('OB_DISPLAY_AMAZON_LANG', openbook_wordpress_translate('Amazon'));
define('OB_DISPLAY_AMAZON_TITLE_LANG', openbook_wordpress_translate('View this title at Amazon'));
define('OB_DISPLAY_GOODREADS_LANG', openbook_wordpress_translate('Goodreads'));
define('OB_DISPLAY_GOODREADS_TITLE_LANG', openbook_wordpress_translate('View this title at Goodreads'));
define('OB_DISPLAY_GOOGLEBOOKS_LANG', openbook_wordpress_translate('Google Books'));
define('OB_DISPLAY_GOOGLEBOOKS_TITLE_LANG', openbook_wordpress_translate('View this title at Google Books'));
define('OB_DISPLAY_LIBRARYCONGRESS_LANG', openbook_wordpress_translate('Library of Congress'));
define('OB_DISPLAY_LIBRARYCONGRESS_TITLE_LANG', openbook_wordpress_translate('View this title at The Library of Congress'));
define('OB_DISPLAY_LIBRARYTHING_LANG', openbook_wordpress_translate('LibraryThing'));
define('OB_DISPLAY_LIBRARYTHING_TITLE_LANG', openbook_wordpress_translate('View this title at LibraryThing'));
define('OB_DISPLAY_WORLDCAT_LANG', openbook_wordpress_translate('WorldCat'));
define('OB_DISPLAY_WORLDCAT_TITLE_LANG', openbook_wordpress_translate('View this title at WorldCat'));
define('OB_DISPLAY_PROJECTGUTENBERG_LANG', openbook_wordpress_translate('Project Gutenberg'));
define('OB_DISPLAY_PROJECTGUTENBERG_TITLE_LANG', openbook_wordpress_translate('View this title at Project Gutenberg'));
define('OB_DISPLAY_BOOKFINDER_LANG', openbook_wordpress_translate('BookFinder'));
define('OB_DISPLAY_BOOKFINDER_TITLE_LANG', openbook_wordpress_translate('Search for the best price at BookFinder'));

?>
