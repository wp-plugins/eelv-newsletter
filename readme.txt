=== EELV newsletter ===
Contributors: 8457, ecolosites
Donate link: 
Tags: newsletter, email
Requires at least: 3.1
Tested up to: 3.4
Stable tag: /trunk
License: CC BY-NC 3.0
License URI: http://creativecommons.org/licenses/by-nc/3.0/

Add a registration form on FrontOffice
a newsletter adminer on BackOffice :
- manage skins
- address book
- archives

== Description ==

Add a registration form on FrontOffice
a newsletter adminer on BackOffice :
- manage skins
- address book
- archives

the plugin allows to use shortcodes into the newsletters

Go to the configuration page on the wordpress admin to manage defaults values for expeditor and fill the unsuscribe page (the page wich contains the shortcode [eelv_news_form] to allow visitors to suscribe/unsuscribe your newsletter)

You can create skins or use the default skin. The default skin automaticly load the 3 latest posts into your newsletter.

Network admins can also setup an email to receive each newsletter sent by the server

== Installation ==

1. Upload `eelv_newsletter` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress admin
3. Create a new page and insert the short code `[eelv_news_form]`
4. You can edit defaults settings in Newsletter > Configuration and help

== Frequently asked questions ==

= Does the adress book has a blacklist ? =

Yes, email registered in the black list, won't receive any newsletter from your site.

= Can I create my own skins ? =

Yes, skins are registered as post-type. 
* Site admins can create skins into newsletter > skins
* To add skins on each blog on a multisite network, just add some items to the default themes variable in your functions.php ex: `$eelv_nl_default_themes['your skin name']='Your skin HTML here';`
* To add a default content to this skin, add an item to the default content variable. ex : `$eelv_nl_content_themes['your skin name']='Your content here';`


== Screenshots ==

http://ecolosites.eelv.fr/files/2012/10/newsletter.png
http://ecolosites.eelv.fr/files/2012/10/newsletter2.png

== Changelog ==

v 2.7.2
* Add: enhanced english translation

v 2.7.1
* Fix: Sql error [Multiple primary key defined] dbdelta

v 2.7.0
* Add: Archives are displayed in a new, blank and clean page
* Fix: Select-box to insert pages into a newsletter was listing posts

v 2.6.7
* Add: Custom content now works for creation, not just edit newsletter

V 2.6.6
* Fix: Performances optimisation 

V 2.6.5
* Fix: broken link for stylesheet

V 2.6.4
* Add: custom column for archives to display queue and sent
* Add: Choice of the archive status : publish or private (for tests) 
* Add: Some more lines for completing translation, soon soon!

V 2.6.3
* Add: custom email to receive a copy of any campaign send (manage it in the network admin)
* Add: Performances optimisation 
* Add: Add some lines for completing translation, not finished yet !

V 2.6.2
* Fix: broken link for status icons

v 2.6
* Publication in the wordpress repository

== Upgrade notice ==

No particular informations

== Languages ==

Français	fr_FR	100%
English		en_US	65%
