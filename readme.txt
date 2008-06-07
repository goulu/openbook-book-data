=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, bookreviews, library, libraries
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 1.0

OpenBook displays a book cover image, title, author, and publisher inside posts or pages using data from Open Library 

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. 

Open Library (http://openlibrary.org) is a neutral source of book data. Best of all, you can add titles and modify the data. It's like Wikipedia for books.

You can direct questions to me, John. Contact me at http://johnmiedema.ca or openbook@johnmiedema.ca.

== Installation ==

1. Upload `openbook.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In your post, insert tags, [openbook][/openbook].
4. In between the tags, place an ISBN number, either 10-digit or 13-digit, like so: [openbook]0836917952[/openbook]. You can look up the ISBN by searching for the book at Open Library, or using other sources. 
5. Type your content as usual after the tags.

When the post is displayed, it will include a book cover image, title, author(s), and publisher(s) in the upper left corner. The cover image and title will link to the full entry on Open Library. 

== Frequently Asked Questions ==

= What if the cover image or data is missing in Open Library? =

If an image is missing, OpenBook will show a blank for the image. If the author is missing, OpenBook will use the "By Statement" or "Contributions". But why not go to the Open Library website and add the data!

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

