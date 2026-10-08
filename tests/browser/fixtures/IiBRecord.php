<?php

namespace Restruct\IiBrowser;

use Restruct\InfoField\InlineInfoField;
use SilverStripe\ORM\DataObject;

/**
 * BROWSER-TEST FIXTURE ONLY - a record edited in a GridField detail form (IiBPage's "Records" tab),
 * whose form is Form_ItemEditForm rather than the page editor's Form_EditForm (#3). An
 * InlineInfoField is aimed at Title, and a Sub_Title field sits before Title: its holder id
 * (..._Sub_Title_Holder) also ends in "_Title_Holder", so a suffix lookup would pick the wrong label.
 *
 * Never loaded by a real install (tests/browser/ carries a _manifest_exclude marker).
 */
class IiBRecord extends DataObject
{
    # Short table names: no namespaced defaults, MySQL caps table names at 64 characters.
    private static $table_name = 'IiBRecord';

    private static $db = [
        'Sub_Title' => 'Varchar',
        'Title' => 'Varchar',
    ];

    private static $has_one = [
        'Page' => IiBPage::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('PageID');
        # Decoy first, so a suffix match on "_Title_Holder" would find it before Title.
        $fields->changeFieldOrder(['Sub_Title', 'Title']);
        $fields->push(InlineInfoField::create('Title', 'The record <em>title</em>.'));

        return $fields;
    }
}
