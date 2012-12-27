=== EELV Newsletter ===
Contributors: bastho, ecolosites
Donate link: http://eelv.fr/adherer/
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
= Add a registration form on FrontOffice, a newsletter adminer on BackOffice : =
* manage skins
* address book
* archives

= Freely manage content =
* shortcuts to add last posts or pages preview
* the plugin allows to use shortcodes into the newsletters

= Suscribe / unsuscribe form =
* Go to the configuration page on the wordpress admin to manage defaults values for expeditor and fill the unsuscribe page
(the page wich contains the shortcode [eelv_news_form] to allow visitors to suscribe/unsuscribe your newsletter)

= Use skins =
* You can create skins or use the default skin.
* The default skin automaticly load the 3 latest posts into your newsletter.

= Sending alert =
* Network admins can also setup an email to receive each newsletter sent by the server, for preventing spam usage or support users

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
<ol> 
<li>Site admins can create skins into newsletter > skins</li>
<li>To add skins on each blog on a multisite network, just add some items to the default themes variable in your functions.php ex: `$eelv_nl_default_themes['your skin name']='Your skin HTML here';`</li>
<li>To add a default content to this skin, add an item to the default content variable. ex : `$eelv_nl_content_themes['your skin name']='Your content here';`</li>
</ol>


== Screenshots ==

<img src="http://ecolosites.eelv.fr/files/2012/10/newsletter.png" alt="newsletter.png"/>
<img src="http://ecolosites.eelv.fr/files/2012/10/newsletter2.png" alt="newsletter.png"/>

== Changelog ==

= 3.1.2 =
* Minor add : Shows percentage rate of reading
* Fix : Issue while checking reading status of an email
* Fix : Enhance functions compability with has_cap
* Fix : Performances optimisations

= 3.1.0 =
* Add : Reading tracking : now appears in archives list
* Fix : Archive front page override other post-types 

= 3.0.0 =
* Add : Reading tracking : try to know if sent emails is readen by recepient (optional)
* Add : Enhanced status icons
* Add : Enhanced english translation

= 2.9.0 =
* Add : Optionnal share links
* Add : Enhanced english translation

= 2.8.0 =
* Add : Higlight unsuscribe link in configuration page
* Add : Enhanced english translation
* Fix : Minor HTML bugs in back-office

= 2.7.4 =
* Fix: Archive display centered

= 2.7.3 =
* Fix: Rename some functions for preventing uncompability

= 2.7.2 =
* Add: Enhanced english translation

= 2.7.1 =
* Fix: Sql error [Multiple primary key defined] dbdelta

= 2.7.0 =
* Add: Archives are displayed in a new, blank and clean page
* Fix: Select-box to insert pages into a newsletter was listing posts

= 2.6.7 =
* Add: Custom content now works for creation, not just edit newsletter

= 2.6.6 =
* Fix: Performances optimisation 

= 2.6.5 =
* Fix: broken link for stylesheet

= 2.6.4 =
* Add: custom column for archives to display queue and sent
* Add: Choice of the archive status : publish or private (for tests) 
* Add: Some more lines for completing translation, soon soon!

= 2.6.3 =
* Add: custom email to receive a copy of any campaign send (manage it in the network admin)
* Add: Performances optimisation 
* Add: Add some lines for completing translation, not finished yet !

= 2.6.2 =
* Fix: broken link for status icons

= 2.6 =
* Publication in the wordpress repository

== Upgrade notice ==

No particular informations

== Languages ==

= Français  =
* fr_FR : 100%

= English =
* en_US	: 65%
