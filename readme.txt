=== ACF Frontend display ===
Contributors: gdurtan
Tags: ACF, Advanced Custom Field, Frontend, display form
Requires at least: 3.1
Tested up to: 4.0
Stable tag: 2.0.6
License: GPLv2 or later

ACF Frontend display - plugin to display ACF form on frontend your site

== Description ==

ACF Frontend display - checking option field to render when you create ACF form.

Major features in ACF Frontend display include:

* Automatically display ACF form on content.

== Frequently Asked Questions ==

No questions yet.

== Installation ==

Upload the ACF Frontend display plugin to your blog, Activate it.

1, 2, 3: You're done!

== Changelog ==
= 2.0.6 =
*  [Security update] removed js file uploader library (due to its security issue), two fields deleted: upload file and mass upload files. 

= 2.0.5 =
* add ob_start to render form on content (fixes many problems with shordcodes and other content modyfications)

= 2.0.4 =
* add action=edit redirect to frontend edit

= 2.0.2 =
* Downgrade display methods to native ACF functions

= 1.3.3 =
* Rebuild AJAX options - display form by id, init form from js, defined caalback, integrate output with rest-api shemas, check googlemap script
* Add multi relations field
* Add date-and-time picker

= 1.3.2 =
* Update display options (groups and visual)
* Add AJAX options

= 1.2.0 =
* New globals properties politics (don't unlock objects, and overdrive globals dynamicly)
* Cloning properties from globalized forms.

= 1.1.6 =
* Add acces to roles
* Fix bootstrap and standard display
* Fix shordcodes crashed on other pages
* Add frontend mass files upload field
* Add frontend datepicker
* Better display - left, top or only placeholders
* Fix fields required on frontend

= 1.0.6 =
* Hidden field update message
* Hidden field message check conditional logic
* Rebuild afd_frontend_display method to implement display templates in future
* Remove "create_post" option (extended prop) to Form Actions plugin
* Fix display lalbel with hidden field 

= 1.0.5 =
* Create this version, because wprepo still download version 1.0.3. I add this ver tag and check

= 1.0.4 =
* Remove fields validation from backent to frontend 

= 1.0.3 =
* Add frontend file upload field
* Add afd_attached_forms_array() API method

= 1.0.2 =
* Fix JS errors with Conditional Logic
* Add field poolAB
* Add global frontend properties

= 1.0.1 =
* Add {afd} namespaces to variables and methods
* Add plugin screenshots 
* rename acf_frontend_form() and acf_form_permision() to afd_frontend_form() and afd_form_permision()

= 1.0.0 =
* Add metabox into pages and posts to activate ACF frontend form.
* Add two extended API method: acf_frontend_form() and acf_form_permision() 
* Add extra form to set arguments into acf_frontend_form()
* Check link between ACF form and post. Is false message about it.

== Screenshots ==

1. Display ACF form on your blog


