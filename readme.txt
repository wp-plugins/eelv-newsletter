=== EELV Newsletter ===
Contributors: bastho, ecolosites
Donate link: http://eelv.fr/adherer/
Tags: newsletter, email, tracking, addressbook, mailing
Requires at least: 3.1
Tested up to: 3.5.2
Stable tag: /trunk
License: CC BY-NC 3.0
License URI: http://creativecommons.org/licenses/by-nc/3.0/

Add a registration form on FrontOffice
a newsletter adminer on BackOffice :
- manage skins
- address book
- archives
- answers

== Description ==
= Add a registration form on FrontOffice, a newsletter manager on BackOffice : =
* manage skins
* address book
* archives
* answers

= Freely manage content =
* shortcuts to add last posts or pages preview
* the plugin allows to use shortcodes into the newsletters

= Suscribe / unsuscribe form =
* Create a new page and put the shortcode [eelv_news_form] in the content.
* Go to the configuration page on the wordpress admin to manage defaults values for expeditor and fill the unsuscribe page
(the page wich contains the shortcode [eelv_news_form] to allow visitors to suscribe/unsuscribe your newsletter)
* available attributes :
<ul>
<li>group=1 (Int, define in wich address book group to register e-mail addresses)</li>
<li>suscribe=1 (bool)</li>
<li>unsuscribe=1 (bool)</li>
<li>archives=1 (Bool, displays or not the archives link)</li>
<li>archives_title="Last newsletters"</li>
</ul>

= Answer functionnality =
* Create a new page and put the shortcode [nl_reply_form] in the content.
* Go to the configuration page on the wordpress admin to manage defaults values for expeditor and fill the answer page
(the page wich contains the shortcode [nl_reply_form] to allow visitors to answer your newsletter)
* add answer links in your newsletters by adding the shortcode [nl_reply_link], you can add as many links as you want.
Attributes your anwser links are rep="the_answer_code" val="the link's text"
example: 
<h4>Do you like this plugin ?</h4>
<p>`[nl_reply_link val="Yes I do" rep="yes"]`</p>
<p>`[nl_reply_link val="it's fine" rep="fine"]`</p>
<p>`[nl_reply_link val="Not at all" rep="no"]`</p>
Links will be automaticly created with the e-mail address of the receipient. If the link is broken, with not enougth datas, a form will be displayed to complete it.

= Reading tracking =
* Check if your newsletters are readen or not
* Clean your address book

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

= Todo =
* Add : thumbnail size configuration
* Add : Archive widget
* Add : Drag'n'drop editor tool

= 3.5.0 =
* Add : News-type taxonomy
* Add : Group destination for suscribe-form (shortcode attribute)
* Fix : Minor Cross Site Scripting (XSS) in group management page
* Fix : better HTML syntax in suscribe form
* Fix : some words

= 3.4.3 =
* Add : Mime format for correct plain text displaying
* Add : show a thickbox for answer links in a preview page
* Fix : shows a correction
* Fix : alerts text correction

= 3.4.2 =
* Add : Check for missing parameters in sending form
* Fix : Help page duplicate content
* Fix : Translation fix
* Fix : SQL syntax error in answer page
* Fix : PHP warning in configuration page

= 3.4.1 =
* Fix : french translation fix
* Fix : edit options version

= 3.4.0 =
* Add : Answer functionnality : requires to create an anwser page with the shortcode [nl_reply_form]
* Fix : enhance archives columns view
* Fix : spy image headers

= 3.3.4 =
* Fix : Hide new available options from network

= 3.3.3 =
* Add : Display an alert for new available options

= 3.3.2 =
* Add : Adjust capabilities for Admins, Editors (can'nt edit configuration or reload parameters) and Authors (can'nt send newsletters)

= 3.3.1 =
* Fix : Remove HTML tags from suscribe-from alerts, JS XSS vulnerability fix

= 3.3.0 =
* Add : Move plugin to English and then retranslate it into french... :-/
* Add : Change newsletter skin directly from send page
* Add : Send a confirmation e-mail to (un)suscribers
* Add : Some help and legend
* Fix : Replace deprecated functions
* Fix : Remove some php warnings 

= 3.2.2 =
* Fix : Check if apply_filter('the_content') doesn't make content empty

= 3.2.1 =
* Fix : CSS fix in admin

= 3.2.0 =
* Add : Title and share-links options for converting posts into newsletters
* Add : Option for hidding archives link under subscription form

= 3.1.5 =
* Fix : bug in configuration page

= 3.1.4 =
* Minor add : duplicate en_US translation to en_UK

= 3.1.3 =
* Minor add : Enhanced english translation

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
* en	: 95%
* en_UK	: 95%
* en_US	: 95%
