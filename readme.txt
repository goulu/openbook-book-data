=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, book reviews, library, libraries, book covers
Requires at least: 2.5.1
Tested up to: 2.6
Stable tag: trunk

OpenBook displays a book cover image, title, author, and publisher inside posts or pages using data from Open Library 

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. 

OpenBook gets its covers and book data from Open Library (http://openlibrary.org), the only source of bibliographic data that is both open source and open data, hence the OpenBook label. Open source is important because the technical knowledge is shared for everyone's good. Open data means that anyone can add and modify titles; this is especially good for independent publishers that might not get represented elsewhere. It's like Wikipedia for books.

Important: To use OpenBook, your server must use PHP 5 or higher. Upgrading to PHP 5 may be a simple matter on the control panel of your server (e.g., Netfirms, http://support.netfirms.com/article.php?id=713).

Latest Update: Version 1.6 beta. Can place multiple covers in the same post or page. Compatible with WordPress shortcodes. Uses thumbnails only to ensure copyright compliance. Displays nicely when imported, e.g., Facebook, Bloglines.

See samples and find support at http://johnmiedema.ca/openbook-wordpress-plugin/. Contact me at openbook@johnmiedema.ca.

== Installation ==

1. Delete any previous version of openbook in the `/wp-content/plugins/` directory
2. Upload the entire openbook-book-data folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. In your post or page, insert the openbook tags and an ISBN number, like so: [openbook booknumber="0864921535"]. You can obtain the ISBN by searching for the book at Open Library or any source of book data.
5. Type your content as usual after the tags

When the post is displayed, it will include a book cover image, title, author(s), and publisher(s) in the upper left corner. The cover image and title will link to the full entry on Open Library. 

== Frequently Asked Questions ==

* What if the title is not in Open Library? Or does not have an ISBN?

Open Libary allows you to add titles, but they currently have a delay in indexing the new title in their search engine. With version 1.2 beta of OpenBook, you can use the Open Library number found in the Open Library URL, e.g., [openbook]/b/OL882707M[/openbook] from http://openlibrary.org/b/OL882707M.

* What if the cover image or other data is missing in Open Library?

If an image is missing, OpenBook will show a blank for the image. If the author is missing, OpenBook will use the "By Statement" or "Contributions". But why not go to the Open Library website and add the data!

* What happens if Open Library is down or unavailable?

Version 1.3 beta of OpenBook detects when Open Library is down and inserts a message where the data would go: "Open Library Data Unavailable". When Open Library becomes available, the book data will be displayed normally.

* Earlier versions of OpenBook used tags like this: [openbook]0864921535[/openbook]. Does this still work?

The old tags still work, but version 1.6 of OpenBook switch to the WordPress shortcode format because it is easier and compatible with other WordPress shortcodes. See the Options sections for more information. You can use either format, or even mix them if you want. It is recommended that you use the shortcode format, e.g., [openbook booknumber="0864921535"].

* Is it legal to copy book covers?

Courts have repeatedly found that thumbnail or reduced versions of artwork fall under fair use provisions of copyright law. Even if that were not so, most publishers are eager to have people use covers to help promote the sale of their books. OpenBook only displays thumbnail versions of book covers.

== Options ==

OpenBook has extra options you can control by adding attributes to the [openbook] tag.

Book Version. Sometimes there are multiple versions of a book in Open Library for a single ISBN. By default, OpenBook uses the most recent version. If you prefer an earlier version specify it like so: 

[openbook booknumber="0864921535" bookversion="2"]

Display Options. OpenBook displays both the cover image and text data by default. The following options are also available: 1=cover only. 2=Text only.

[openbook booknumber="0864921535" displayoptions="1"]

To display multiple options, such as bookversion and displayoptions:

[openbook booknumber="0864921535" bookversion="2" displayoptions="1"]

Publisher Link. If you supply a publisher link, the publisher name will link to that site:

[openbook booknumber="0864921535" publisherlink="http://www.gooselane.com/"]

Anchor Attributes. If you want your links to open in a new window, you can specify "target=_blank" like so:

[openbook booknumber="0864921535" anchorattributes="target=_blank"]

Open Library Timeout. The default timeout for connecting to Open Library and completing the call is ten seconds. You can change this timeout to another value:

[openbook booknumber="0864921535" curltimeout="30"]

Hide Library. If you do not want a link to WorldCat:

[openbook booknumber="0864921535" hidelibrary="true"]

Note too that you can place as many OpenBook tags as you like in a post or page.

== Future Release Plans ==

* I'm debating if I should remove all default styling to let the user decide. Your opinion?

* New option to link to an author's and/or artist/illustrator's website.

* Rolling the cursor over the book cover will show description, first sentence, notes if available in Open Library.

* Minor: Add title prefixes from Open Library.
