=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, book reviews, library, libraries, book covers, COinS
Requires at least: 2.5.1
Tested up to: 2.7
Stable tag: trunk

OpenBook displays a book cover image, title, author, and publisher inside posts or pages using data from Open Library 

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. 

OpenBook gets its covers and book data from Open Library (http://openlibrary.org), the only source of bibliographic data that is both open source and open data, hence the OpenBook label. Open source is important because the technical knowledge is shared for everyone's good. Open data means that anyone can add and modify titles; this is especially good for independent publishers that might not get represented elsewhere. It's like Wikipedia for books.

Important: To use OpenBook, your server must use PHP 5 or higher. Upgrading to PHP 5 may be a simple matter on the control panel of your server (e.g., Netfirms, http://support.netfirms.com/article.php?id=713).

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

* Are there any display or other options?

There are many options. After installing OpenBook, see the OpenBook administration panel in WordPress to get more information.

* Earlier versions of OpenBook used tags like this: [openbook]0864921535[/openbook]. Does this still work?

The old tags still work, but version 1.6 of OpenBook switch to the WordPress shortcode format because it is easier and compatible with other WordPress shortcodes. See the Options sections for more information. You can use either format, or even mix them if you want. It is recommended that you use the shortcode format, e.g., [openbook booknumber="0864921535"].

* Is it legal to copy book covers?

Courts have repeatedly found that thumbnail or reduced versions of artwork fall under fair use provisions of copyright law. Even if that were not so, most publishers are eager to have people use covers to help promote the sale of their books. OpenBook only displays thumbnail versions of book covers.

* How do I use the COinS feature?

OpenBook inserts COinS data in the HTML that other applications can read the bibliographic data. It is useful for applications like Zotero, an open source reference manager.

== Future Release Plans ==

* Administrative panel will allow user to set options.

* Link to full-text on-line when available from Open Library.

* Link to LibraryThing for social data.

* New option to link to an author's and/or artist/illustrator's website.

* I'm not quite happy with the styling defaults yet, e.g., float. Still thinking about this.

* PHP4 compatibility, if there is interest. Let me know.

* Always open to suggestions ...

