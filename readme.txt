=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, book reviews, library, libraries, book covers, COinS, OpenURL
Requires at least: 2.5.1
Tested up to: 2.9
Stable tag: 2.7.1

Displays a book's cover image, title, author, publisher and other book data from Open Library.

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. Insert an OpenBook 'shortcode' with a book number in a WordPress post, page or widget, and OpenBook will display a book cover image, author, and other book data from Open Library (http://openlibrary.org). It also displays links to book websites. Users can control the content and styling through templates. Librarians can point OpenBook to their library records using an OpenURL resolver. 

Requirements. To use OpenBook, your server must use PHP 5 or higher, and cURL must be enabled. 

== Installation and Basic Use ==

1. Deactivate any previous version of OpenBook through the 'Plugins' menu in WordPress.
2. Delete any previous version of OpenBook in the `/wp-content/plugins/` directory.
3. Upload the entire openbook folder to the `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. In your post, page, or text widget, insert the openbook tags and an ISBN number, like so: [openbook booknumber="0864921535"].
6. Type your content as usual after the tags

By default, OpenBook will display a book cover image, title, author, and publisher, along with links to Open Library, WorldCat, and other book sites.

== Frequently Asked Questions ==

* Where do I find an ISBN number?

You can obtain the ISBN for a book by searching for it in Open Library. It is also usually listed in other common sources of book data, e.g., Amazon.

* What if the title is not in Open Library? 

You can add titles to Open Library. It is like Wikipedia for books.

* What if the title does not have an ISBN?

You can use the Open Library number found in the Open Library URL, e.g., [openbook]/b/OL882707M[/openbook] from http://openlibrary.org/b/OL882707M.

* What if the cover image or other data is missing in Open Library?

If an image is missing, OpenBook will show a blank for the image. You can add cover images and other data to Open Library.

* What happens if Open Library is slow, down, or unavailable?

Open Library's cover and/or data servers may be slow, down or otherwise unavailable for periods of time. OpenBook timeouts in ten seconds (or the value configured in Settings) and displays a message where the data would normally go: "Open Library Data Unavailable". When Open Library becomes available, the book data will be displayed normally.

* How do I change the display of OpenBook?

Change the content, ordering and styling of OpenBook using the templates in the Settings panel for OpenBook.

* How do I point OpenBook to my library?

In the OpenBook Settings panel, configure an OpenURL resolver for your library.

* Is it legal to copy book covers?

Publishers generally like that people use their cover images because it promotes book sales. This fact does not necessarily protect the rights of cover illustrators. Also, people may be able to upload covers to Open Library for which they do not have rights. In some countries like the United States, thumbnail representations of artwork fall under fair use provisions of copyright law. The size of a thumbnail varies, but all of the book cover images in Open Library are reduced size. Users of OpenBook are advised to comply with their local laws. If a publisher, illustrator or other rights-holder asks you to take down a cover image, please do so. 

* Where do I get more detailed help?

See the OpenBook support site at http://code.google.com/p/openbook4wordpress/ for detailed help and support. I encourage to report problems at http://code.google.com/p/openbook4wordpress/issues/list. You can also check my website, http://johnmiedema.ca/openbook-wordpress-plugin/, or email me at openbook@johnmiedema.ca.
