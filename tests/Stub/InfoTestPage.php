<?php

namespace Restruct\InfoField\Tests\Stub;

use Restruct\InfoField\InfoField;
use Restruct\InfoField\InlineInfoField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\TestOnly;

# silverstripe/cms is not a requirement of this module (it only needs framework), so this stub must
# not be declared without it: in test mode the tests/ dir is in the class manifest, and a class
# extending a missing parent fatals the manifest rebuild in every consuming project. The guard
# sits AFTER the use imports so SiteTree::class resolves to the real FQCN.
if (!class_exists(SiteTree::class)) {
    return;
}

/**
 * Page type used by CmsRenderTest to render both fields inside a real CMS edit form.
 */
class InfoTestPage extends SiteTree implements TestOnly
{
    # Short table name: a stub under the full test namespace makes long table names.
    private static $table_name = 'InfoTestPage';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        # The inline field is attached to the Title field's label client-side, by data-target
        $fields->addFieldToTab('Root.Main', InlineInfoField::create('Title', 'Inline help for the title'));
        $fields->addFieldToTab('Root.Main', InfoField::create('BoxInfo', 'Boxed help text'), 'Title');

        return $fields;
    }
}
