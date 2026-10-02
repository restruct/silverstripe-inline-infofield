<?php

namespace Restruct\IiBrowser;

use Restruct\InfoField\InfoField;
use Restruct\InfoField\InlineInfoField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\FieldType\DBField;

/**
 * BROWSER-TEST FIXTURE ONLY - a page type whose CMS edit form uses both fields as the README shows:
 * an InfoField with string content and one with object content, and two InlineInfoFields aimed at
 * the Title and MenuTitle labels, pushed at the end of the tab (the script moves them).
 *
 * Never loaded by a real install: it lives under tests/browser/, which carries a _manifest_exclude
 * marker, and the browser-test runner copies it into a scratch host's app/ before dev/build.
 * Written for Silverstripe 5 and 6 alike (no class imports that moved between the two).
 */
class IiBPage extends SiteTree
{
    # Short table names: no namespaced defaults, MySQL caps table names at 64 characters.
    private static $table_name = 'IiBPage';

    private static $singular_name = 'Info browser page';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        # Without the Content editor: the admin's TinyMCE integration throws uncaught errors of its
        # own in a scripted browser (SS5: its scroll handler calls a global $ that is not jQuery; SS6:
        # its textarea's change tracker runs after the editor is gone). Neither involves this module,
        # and the specs fail on any uncaught error.
        $fields->removeByName('Content');

        $fields->addFieldToTab(
            'Root.Main',
            InfoField::create('StringInfo', 'Save the page first, then edit the <b class="iib-bold">blocks</b>.')
                ->addExtraClass('iib-string'),
            'Title'
        );
        # Object content: boxed like a string since 3.0.0 (it used to be output bare).
        $fields->addFieldToTab(
            'Root.Main',
            InfoField::create('ObjectInfo', DBField::create_field('HTMLFragment', '<i class="iib-italic">Rendered</i> content'))
                ->addExtraClass('iib-object')
        );
        $fields->push(InlineInfoField::create('Title', 'Keep titles <em>short</em>: they are also used in the menu.'));
        $fields->addFieldToTab('Root.Main', InlineInfoField::create('MenuTitle', 'Shown in the navigation.'));

        return $fields;
    }
}
