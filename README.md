SilverStripe Inline Help Field
===================================

Adds a question mark next to the label of another field that expands to show text when clicked.  Can be used to add extra information or instructions without taking up space all the time.

Maintainer Contacts
-------------------
* Nathan Cox (<nathan@flyingmonkey.co.nz>)

Requirements
------------
* SilverStripe 2.4+

Documentation
-------------
[GitHub Wiki](https://github.com/nathancox/silverstripe-helpfield)

Installation Instructions
-------------------------

1. Place the files in a directory called helpfield in the root of your SilverStripe installation
2. Visit yoursite.com/dev/build to rebuild the database

Usage Overview
--------------

HelpField takes two parameters:
1. The name of the field you want to add help text to (eg, "MenuTitle" or "Content")
2. The help text itself.  Should be a string of text/HTML

	$fields->addFieldToTab('Root.Content.Main', new HelpField('Content', '
		You can use the following short tags in the content:<br />
		<b>[video]</b> - inserts the video uploaded in the Media tab into the content<br />
		<b>[toaster]</b> - inserts a formatted button that, when clicked, makes toast
	'));


Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-helpfield/issues)