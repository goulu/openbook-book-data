<h1>OpenBook Administration</h1>

<p>This page is the beginning of an Administration panel for OpenBook. For now, it just lists the options. Lots more coming.</p>

<p>OpenBook has extra options you can control by adding attributes to the [openbook] tag.

<p><b>Book Version.</b> Sometimes there are multiple versions of a book in Open Library for a single ISBN. By default, OpenBook uses the most recent version. If you prefer an earlier version specify it like so: 

<p>[openbook booknumber="0864921535" bookversion="2"]

<p><b>Display Options.</b> OpenBook displays both the cover image and text data by default. The following options are also available: 1=cover only. 2=Text only.

<p>[openbook booknumber="0864921535" displayoptions="1"]

<p>To display multiple options, such as bookversion and displayoptions:

<p>[openbook booknumber="0864921535" bookversion="2" displayoptions="1"]

<p><b>Publisher Link.</b> If you supply a publisher link, the publisher name will link to that site:

<p>[openbook booknumber="0864921535" publisherlink="http://www.gooselane.com/"]

<p><b>Anchor Attributes.</b> If you want your links to open in a new window, you can specify "target=_blank" like so:

<p>[openbook booknumber="0864921535" anchorattributes="target=_blank"]

<p><b>Open Library Timeout.</b> The default timeout for connecting to Open Library and completing the call is ten seconds. You can change this timeout to another value:

<p>[openbook booknumber="0864921535" curltimeout="30"]

<p><b>Hide Library.</b> If you do not want a link to WorldCat:

<p>[openbook booknumber="0864921535" hidelibrary="true"]

<p><b>Small Cover.</b> To show a small cover instead of the default medium-sized cover, use the following. When using this option, you may also want to set hidelibrary="true" for cleaner formatting.

<p>[openbook booknumber="0864921535" smallcover="true"]

<p>Note too that you can place as many OpenBook tags as you like in a post or page.


