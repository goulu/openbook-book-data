=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, bookreviews, library, libraries
Requires at least: 2.5
Tested up to: 2.6
Stable tag: trunk

OpenBook displays a book cover image, title, author, and publisher inside posts or pages using data from Open Library 

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. 

Open Library (http://openlibrary.org) is the only source of bibliographic data that is both open source and open data, hence the OpenBook label. This means that the technical knowledge is shared for everyone's good. It also means anyone can add and modify titles; this is especially good for independent publishers that might not get represented elsewhere. It's like Wikipedia for books.

Important: To use OpenBook, your server must use PHP 5 or higher. Upgrading to PHP 5 may be a simple matter on the control panel of your server (e.g., Netfirms, http://support.netfirms.com/article.php?id=713). Otherwise, look for a future version of OpenBook.

Latest Update: Version 1.4 beta. Works with PHP 5+ (not just 5.2).

Please don't hesitate to contact me with any error reports or questions. Contact me at http://johnmiedema.ca or openbook@johnmiedema.ca.

== Installation ==

1. Delete any previous version of openbook in the `/wp-content/plugins/` directory
2. Upload the entire openbook-book-data folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. In your post, insert tags, [openbook][/openbook].
5. In between the tags, place an ISBN number, either 10-digit or 13-digit, like so: [openbook]0836917952[/openbook]. You can look up the ISBN by searching for the book at Open Library, or using other sources. 
6. Type your content as usual after the tags.

When the post is displayed, it will include a book cover image, title, author(s), and publisher(s) in the upper left corner. The cover image and title will link to the full entry on Open Library. 

== Frequently Asked Questions ==

* What if the title is not in Open Library? Or does not have an ISBN?

Open Libary allows you to add titles, but they currently have a delay in indexing the new title in their search engine. With version 1.2 beta of OpenBook, you can use the Open Library number found in the Open Library URL, e.g., [openbook]/b/OL13489256M[/openbook] from http://openlibrary.org/b/OL13489256M.

* What if the cover image or other data is missing in Open Library?

If an image is missing, OpenBook will show a blank for the image. If the author is missing, OpenBook will use the "By Statement" or "Contributions". But why not go to the Open Library website and add the data!

* What happens if Open Library is down or unavailable?

Version 1.3 beta of OpenBook detects when Open Library is down and inserts a message where the data would go: "Open Library Data Unavailable". When Open Library becomes available, the book data will be displayed normally.

== Options ==

There are few extra options you can control if you want by adding extra arguments between the tags. 

Book Version. Sometimes there are multiple versions of a book in Open Library for a single ISBN. By default, OpenBook uses the most recent version. If you prefer an earlier version, insert the version number as a second argument, like so: 

[openbook]0836917952,2[/openbook]

Display Options. OpenBook displays both the cover image and text data by default. The following options are also available: 1=cover only. 2=Text only. Insert this value as a third argument:

[openbook]0836917952,2,1[/openbook]

If you want to skip the second argument, just put a comma in to hold the place: [openbook]0836917952,,1[/openbook]

Full Cover. OpenBook shows a reduced sized version of the source image by default. If you want the orignal size, set the fourth argument to true:

[openbook]0836917952,,,true[/openbook]

Publisher Link. If you include a link to a publisher's website as the fifth argument, the publisher name will be wrapped in that link:

[openbook]0836917952,,,,http://randomhouse.ca[/openbook]

Anchor Attributes. If you want your links to open in a new window, you can specify "target=_blank" here, without the quotes:

[openbook]0836917952,,,,,target=_blank[/openbook]

Open Library Timeout. The default timeout for connecting to Open Library and completing the call is ten seconds. You can change this timeout:

[openbook]0836917952,,,,,,30[/openbook]

== Future Release Plans ==

* Allow multiple tags in one page or post. The current version only picks up the first set of tags. A future version will allow any number of tags.

* Insert data in the location of the tags. The current version places the data in the upper left corner. A future version will place the data wherever the tags are located.


