=== EELV Newsletter ===
Contributors: bastho, ecolosites
Donate link: http://eelv.fr/adherer/
Tags: newsletter, email, tracking, addressbook, mailing
Requires at least: 3.8
Tested up to: 3.9.1
Stable tag: /trunk
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a registration form on Front-office
a newsletter adminer on BackOffice : manage skins, address book, archives, answers, tracking

== Description ==
= Add a registration form on FrontOffice, a newsletter manager on BackOffice : =
* manage skins
* address book
* archives
* answers

= Freely manage content =
* shortcuts to add last posts or pages preview
* the plugin allows to use shortcodes into the newsletters

= Subscribe / unsubscribe form =
* Create a new page and put the shortcode [eelv_news_form] in the content.
* Go to the configuration page on the wordpress admin to manage defaults values for expeditor and fill the unsuscribe page
(the page wich contains the shortcode [eelv_news_form] to allow visitors to suscribe/unsuscribe your newsletter)
* available attributes :
<ul>
<li>group=1 (Int, define in wich address book group to register e-mail addresses)</li>
<li>subscribe=1 (bool)</li>
<li>unsubscribe=1 (bool)</li>
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
<blockquote>
<b>Do you like this plugin ?</b>
<p>`[nl_reply_link val="Yes I do" rep="yes"]`</p>
<p>`[nl_reply_link val="it's fine" rep="fine"]`</p>
<p>`[nl_reply_link val="Not at all" rep="no"]`</p>
Links will be automaticly created with the e-mail address of the receipient. If the link is broken, with not enougth datas, a form will be displayed to complete it.
</blockquote>

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

1. Template manager
2. Sending options

== Changelog ==

= Todo =
* Add: Drag'n'drop editor tool
* Add: Import/export in address books

= 3.8.7 =
* Fix: JS error on subscribe form from shortcode

= 3.8.6 =
* Fix: bug on archives list front page

= 3.8.5 =
* Fix: Change headers end of line to CRLF (\r\n) to match more servers
http://fr2.php.net/manual/fr/function.mail.php
* Fix: Remove some PHP warnings

= 3.8.4 =
* Fix: Activation generate error

= 3.8.3 =
* Fix: subscription widget bug

= 3.8.1 =
* Fix: Break lines after inserted posts in newsletter editor to prevent posts in other posts

= 3.8.0 = 
* Add: More options in skin management and for default content
* Add: Remove autoP formating for skins and newsletters
* Add: 2 cols preformated template
* Add: Ability to load skin's default content unregardless to the selected skin
* Add: Displays real posts in realtime preview for default content
* Fix: Refresh after selecting a skin in newsletter editing

= 3.7.0 = 
* Add: Edit default content for each newsletter skin
* Add: Edit item style for each default content with realtime preview
* Add: Archives widget
* Add: Better address book list displaying
* Add: Update the online newsletter displaying
* Fix: Huge code improvement
* Fix: Wording & translation
* Fix: Few bugs


= 3.6.7 = 
* Add : 5sec. delay between two bursts
* Add : Custom wpdb query for users adressing in order to get only required datas (improves performances)

= 3.6.6 = 
* Fix : Integration in WP 3.8

= 3.6.5 = 
* Fix : suscription bug

= 3.6.4 = 
* Fix : update edit.php to post.php
* Fix : performances optimisation
* Fix : suscription bug

= 3.6.3 = 
* Fix : Addressing assignation problem

= 3.6.2 = 
* Add : Double verification for users role, for preventing conflict with custom capabilities

= 3.6.1 = 
* Fix : Important bug fix with role selection for receipients
* Add : Add addressing possibility with `login` values
* Fix : French translation

= 3.6.0 = 
* Add : Add addressing possibility with `name` and `email` values
* Add : Default WP `aligncenter`, `alignleft` and `alignright` style support
* Add : Edit spy image's text

= 3.5.9 = 
* Fix : Options stripslashes
* Fix : Address book fields focus

= 3.5.8 =
* Fix : Minor bug fix

= 3.5.7 =
* Fix : Correctly allow multiple form occurences
* Fix : French translation

= 3.5.6 =
* Add : widget options : texts, classes
* Add : widget placeholder replacement for old browsers

= 3.5.5 =
* Add : Pre-checked option for share buttons
* Add : Default style for widget, suscribe/unsuscribe show/hide occurence
* Fix : Auto update option version
* Fix : Change licence from CC to GPL

= 3.5.4 =
* Fix : use wp_enqueue_style function

= 3.5.3 =
* Fix : Adjust color of red-list emails

= 3.5.2 =
* Fix : Keeps newsletter submenu opened on news-type page

= 3.5.1 =
* Fix : User role setting for news-types

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

= 3.8.7 = 
Subscribe form shortcode attributes changed !
suscribe becomes subscribe
unsuscribe becomes unsubscribe

= 3.8.0 = 
AutoP is removed form skins and newsletters. Please, re-edit them.

== Languages ==

= Français  =
* fr_FR : 100%

= English =
* en	: 100%
