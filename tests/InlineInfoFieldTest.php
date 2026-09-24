<?php

namespace Restruct\InfoField\Tests;

use Restruct\InfoField\InlineInfoField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextField;

/**
 * InlineInfoField renders a small span that the module's CMS JavaScript moves into the label of
 * the field named by data-target. The markup is the contract with InlineInfoField.js and .css:
 * `span.inline-info[data-target]` is what the script matches, `.message.info.small.inline-info` is
 * what the stylesheet positions, and the inner span is what is shown on hover or tap.
 */
class InlineInfoFieldTest extends SapphireTest
{
    public function testNameIsDerivedFromTheTargetField()
    {
        $field = InlineInfoField::create('Title', 'Help');

        $this->assertSame('Title_InlineInfoField', $field->getName());
    }

    public function testFieldHolderRendersTheMarkupTheScriptAndStylesheetExpect()
    {
        $field = InlineInfoField::create('MenuTitle', 'Shown in the menu');

        $this->assertSame(
            '<span class="message info small inline-info" data-target="MenuTitle"><span>Shown in the menu</span></span>',
            $field->FieldHolder()
        );
    }

    public function testFieldRendersTheSameAsFieldHolder()
    {
        $field = InlineInfoField::create('Title', 'Help');

        $this->assertSame($field->FieldHolder(), $field->Field());
    }

    public function testContentIsHtmlAndIsNotEscaped()
    {
        $field = InlineInfoField::create('Title', 'Use <b>short</b> titles');

        $this->assertStringContainsString('<span>Use <b>short</b> titles</span>', $field->FieldHolder());
    }

    public function testTargetFieldNameIsEscapedInTheAttribute()
    {
        # The target is a field name, but it is written into an HTML attribute: a quote in it must
        # not be able to break out of data-target
        $field = InlineInfoField::create('Ti"tle', 'Help');

        $this->assertStringContainsString('data-target="Ti&quot;tle"', $field->FieldHolder());
    }

    public function testRendersInsideAFormAlongsideItsTarget()
    {
        $form = Form::create(
            null,
            'TestForm',
            FieldList::create(TextField::create('Title'), InlineInfoField::create('Title', 'Help in a form')),
            FieldList::create()
        );
        $html = (string) $form->forTemplate();

        $this->assertStringContainsString('data-target="Title"><span>Help in a form</span>', $html);
        # ... and the target holder the script looks the label up by still exists alongside it
        $this->assertStringContainsString('id="Form_TestForm_Title_Holder"', $html);
    }

    public function testReadonlyFormStillRendersTheInfo()
    {
        $fields = FieldList::create(TextField::create('Title'), InlineInfoField::create('Title', 'Readonly help'));
        $readonly = $fields->makeReadonly();

        $field = $readonly->dataFieldByName('Title_InlineInfoField') ?: $readonly->fieldByName('Title_InlineInfoField');
        $this->assertNotNull($field);
        $this->assertStringContainsString('<span>Readonly help</span>', $field->FieldHolder());
    }
}
