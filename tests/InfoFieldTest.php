<?php

namespace Restruct\InfoField\Tests;

use Restruct\InfoField\InfoField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\FieldType\DBField;

/**
 * InfoField: a LiteralField whose content is rendered inside a `div.message.info` box.
 */
class InfoFieldTest extends SapphireTest
{
    public function testStringContentIsRenderedInsideAnInfoBox()
    {
        $field = InfoField::create('Help', 'Some help');

        $this->assertSame('<div class="message info ">Some help</div>', $field->FieldHolder());
    }

    public function testContentIsHtmlAndIsNotEscaped()
    {
        # The documented contract is "an arbitrary piece of HTML"
        $field = InfoField::create('Help', '<b>bold</b> and <a href="https://example.com">a link</a>');

        $this->assertStringContainsString(
            '<b>bold</b> and <a href="https://example.com">a link</a>',
            $field->FieldHolder()
        );
    }

    public function testExtraClassesAreAddedToTheBox()
    {
        $field = InfoField::create('Help', 'Some help');
        $field->addExtraClass('warning');
        $field->addExtraClass('wide');

        $this->assertSame('<div class="message info warning wide">Some help</div>', $field->FieldHolder());
    }

    public function testFieldRendersTheSameAsFieldHolder()
    {
        # LiteralField::Field() delegates to FieldHolder(), so the box also appears where a form
        # template calls $Field instead of $FieldHolder
        $field = InfoField::create('Help', 'Some help');

        $this->assertSame($field->FieldHolder(), $field->Field());
    }

    /**
     * Regression: object content (for example a DBHTMLText from DBField::create_field() or
     * renderWith()) was returned bare, without the info box the class exists to provide.
     */
    public function testObjectContentIsAlsoRenderedInsideAnInfoBox()
    {
        $field = InfoField::create('Help', DBField::create_field('HTMLFragment', '<i>rendered</i>'));
        $field->addExtraClass('warning');

        $this->assertSame('<div class="message info warning"><i>rendered</i></div>', $field->FieldHolder());
    }

    public function testReadonlyTransformationStillRendersTheBox()
    {
        # A CMS form made readonly (eg. no edit permission) transforms every field
        $field = InfoField::create('Help', 'Some help')->performReadonlyTransformation();

        $this->assertInstanceOf(InfoField::class, $field);
        $this->assertSame('<div class="message info ">Some help</div>', $field->FieldHolder());
    }

    public function testRendersInsideAForm()
    {
        $form = Form::create(null, 'TestForm', FieldList::create(InfoField::create('Help', 'Help in a form')), FieldList::create());

        $this->assertStringContainsString('<div class="message info ">Help in a form</div>', (string) $form->forTemplate());
    }
}
