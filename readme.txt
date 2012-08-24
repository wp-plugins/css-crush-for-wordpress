=== CSS Crush for WordPress ===
Contributors: codepress, tschutter
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZDZRSYLQ4Z76J
Tags: css, crush, wordpress, pre-processor, minify
Requires at least: 3.1
Tested up to: 3.4
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

CSS allow you to do lots of things, but some features are missing to developers. What about variables, constants, and general faster syntax? Normal CSS can not do that, that is why you have preprocessors, like CSS Crush. 

CSS Crush for WordPress allows the use of variables in CSS files. 

This plugin will automaticly process all your theme's stylesheets for you! No configuration needed. However, you can customize it from the Settings page.

By default your stylesheet will be *minified*, *cached* and have it's vendor *prefixes automatically generated*.

**examples of generated prefixes**

`
/* Before */
div { background: red linear-gradient( left, red, white ); }

/* After */
div {
  background: red -webkit-linear-gradient( left, red, white );
  background: red -moz-linear-gradient( left, red, white );
  background: red -o-linear-gradient( left, red, white );
  background: red linear-gradient( left, red, white );
}
`

**example with the use of variables**

`
/* Defining variables */
@define {
	brand-blue: #C1D9F5;	
	helvetica: "Helvetica Neue", Helvetica, Arial, sans-serif;	
}

ul, p {
	font-family: $( helvetica );
	background: $( brand-blue );
}
`

**More Features**

* Declare variables in your CSS
* Direct @import
* A small collection of custom math functions are built-in
* Block nesting
* The experimental :any pseudo class is supported

*CSS Crush version: 1.5.3*

For a full list of features please visit <a href="http://the-echoplex.net/csscrush/">http://the-echoplex.net/csscrush/</a>.

**Related Links:**

* http://www.codepress.nl/plugins/

= Translations = 

If you like to contribute a language, please send them to <a href="mailto:info@codepress.nl">info@codepress.nl</a>.

== Installation ==

1. Upload css-crush-for-wordpress to the /wp-content/plugins/ directory
2. Activate CSS Crush for WordPress through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the CSS Crush settings that appears under the Settings menu.

== Frequently Asked Questions ==

= Are you the author of CSS Crush? =

NO. We just ported it to WordPress. For more info on the CSS Crush itself, visit it's website: <a href="http://the-echoplex.net/csscrush/">http://the-echoplex.net/csscrush/</a>.

= What new variables can I use in the CSS stylesheet? =

Have a look at the tour section: <a href="http://the-echoplex.net/csscrush/">http://the-echoplex.net/csscrush/</a>.

= I would like to contribute to CSS Crush =

You can fork the original CSS Crush code on Github and submit patches: <a href="https://github.com/peteboere/css-crush">https://github.com/peteboere/css-crush</a>

== Screenshots ==

1. Settings page

== Changelog ==

= 0.1 =

* Initial release.