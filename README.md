# HTML Purifier plugin for DokuWiki

This DokuWiki plugin allows **HTML Purifier** to be used as a filter for embedded HTML code in wiki pages using `<html>` or `<HTML>` tags.

Note: You need first to enable the [htmkok](https://www.dokuwiki.org/config:htmlok) parameter in the DokuWiki configuration. Otherwise, the code is displayed in the page instead of rendered.

## About HTML Purifier
**HTML Purifier** is a standards-compliant HTML filter library written in PHP. HTML Purifier will not only remove all malicious code (better known as XSS) with a thoroughly audited, secure yet permissive whitelist, it will also make sure your documents are standards compliant, something only achievable with a comprehensive knowledge of W3C's specifications.

HTML Purifier's library is licensed under the LGPL v2.1+. see http://htmlpurifier.org/download

