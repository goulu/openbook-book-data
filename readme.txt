=== OpenBook Book Data ===
Contributors: johnmiedema
Tags: book, books, reading, bookreviews, library, libraries
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 1.1

OpenBook displays a book cover image, title, author, and publisher inside posts or pages using data from Open Library 

== Description ==

OpenBook is for book reviewers, book bloggers, library webmasters, anyone who wants to put book covers and data on their WordPress blog or website. 

Open Library (http://openlibrary.org) is a neutral source of book data. Best of all, you can add titles and modify the data. It's like Wikipedia for books.

Please don't hesitate to contact me with any error reports or questions. Contact me at http://johnmiedema.ca or openbook@johnmiedema.ca.

== Installation ==

1. Upload `openbook.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In your post, insert tags, [openbook][/openbook].
4. In between the tags, place an ISBN number, either 10-digit or 13-digit, like so: [openbook]0836917952[/openbook]. You can look up the ISBN by searching for the book at Open Library, or using other sources. 
5. Type your content as usual after the tags.

When the post is displayed, it will include a book cover image, title, author(s), and publisher(s) in the upper left corner. The cover image and title will link to the full entry on Open Library. 

== Frequently Asked Questions ==

* What if the cover image or data is missing in Open Library?

If an image is missing, OpenBook will show a blank for the image. If the author is missing, OpenBook will use the "By Statement" or "Contributions". But why not go to the Open Library website and add the data!

* The book data did not load after a brief delay (about 15 seconds)

I have noticed that the Open Library search is sometimes down for a short while, then comes back up later. This is an availability matter at OpenLibrary. The down time seems infrequent. Likely the data will be there a short while later.

== Future Release Plans ==

* Allow multiple tags in one page or post. The current version only picks up the first set of tags. A future version will allow any number of tags.

* Insert data in the location of the tags. The current version places the data in the upper left corner. A future version will place the data wherever the tags are located.

* Allow other numbers than ISBN. The current version only searches by ISBN. A future version will search by other cataloguing numbers. 

== Options ==

There are few extra options you can control if you want by adding extra arguments between the tags. 

1. Book Version. Sometimes there are multiple versions of a book in Open Library for a single ISBN. By default, OpenBook uses the most recent version. If you prefer an earlier version, insert the version number as a second argument, like so: 

[openbook]0836917952,2[/openbook]

2. Display Options. OpenBook displays both the cover image and text data by default. The following options are also available: 1=cover only. 2=Text only. Insert this value as a third argument:

[openbook]0836917952,2,1[/openbook]

3. If you want to skip the second argument, just put a comma in to hold the place: [openbook]0836917952,,1[/openbook]

4. Full Cover. OpenBook shows a reduced sized version of the source image by default. If you want the orignal size, set the fourth argument to true:

[openbook]0836917952,,,true[/openbook]

5. Publisher Link. If you include a link to a publisher's website as the fifth argument, the publisher name will be wrapped in that link:

[openbook]0836917952,,,,http://randomhouse.ca[/openbook]

6.Anchor Attributes. If you want your links to open in a new window, you can specify "target=_blank" here, without the quotes:

[openbook]0836917952,,,,,target=_blank[/openbook]


