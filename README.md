# opengraph-remixed

This is an extention of the core plugin "opengraph" of Bludit CMS.

It provides a proper open graph implementation based on [webcode tools open graph generator](https://webcode.tools/).
The original plugin didn't support all recommended tags for the properties *article* and *image*.

## example

The full extend of my implelentation looks like this:

```
<!-- Open Graph -->
<meta property="og:locale" content="**locale according CMS settings**">
<meta property="og:type" content="article">
<meta property="og:title" content="**Title of page/static/sticky or name of category/tag**">
<meta property="og:description" content="**SEO description of page/static/sticky or description of category**">
<meta property="og:url" content="**permalink of page**">
<meta property="og:site_name" content="BLUDIT">
<meta property="article:author" content="**Firstname Lastname of author**">
<meta property="article:published_time" content="**publish date of page**">
<meta property="article:section" content="**category assigned to page/static/sticky**">
<meta property="article:tag" content="**tags assigned to page/static/sticky**">
<meta property="og:image" content="**permalink of cover-image**">
<meta property="og:image:secure_url" content="**if permalink of og:image includes https this tag is set**">
<meta property="og:image:type" content="mime-typ of og:image">
<meta property="og:image:width" content="with of og:image">
<meta property="og:image:height" content="height of og:image">
```