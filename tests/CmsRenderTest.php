<?php

namespace Restruct\InfoField\Tests;

use Restruct\InfoField\Tests\Stub\InfoTestPage;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\FunctionalTest;

/**
 * Renders a real CMS page edit form containing both fields, as a logged-in admin. This is as far
 * as a PHP test can see: whether the script then moves the inline info into the label is browser
 * behaviour. What it does pin is everything the script depends on: the module's assets are on
 * the page, the span carries its data-target, and the target's holder has the
 * `Form_EditForm_<Field>_Holder` id and a label that the script looks up.
 */
class CmsRenderTest extends FunctionalTest
{
    protected $usesDatabase = true;

    protected static $extra_dataobjects = [
        InfoTestPage::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        if (!class_exists(SiteTree::class)) {
            $this->markTestSkipped('silverstripe/cms is not installed');
        }
    }

    public function testCmsEditFormRendersTheFieldsAndLoadsTheirAssets()
    {
        $page = InfoTestPage::create(['Title' => 'Info test']);
        $page->write();

        $this->logInWithPermission('ADMIN');
        $response = $this->get($page->CMSEditLink());

        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getBody();

        # Assets, from LeftAndMain.extra_requirements_*
        $this->assertMatchesRegularExpression('#<script[^>]+src="[^"]*/client/dist/js/InlineInfoField\.js#', $body);
        $this->assertMatchesRegularExpression('#<link[^>]+href="[^"]*/client/dist/css/InlineInfoField\.css#', $body);

        # The two fields
        $this->assertStringContainsString(
            '<span class="message info small inline-info" data-target="Title"><span>Inline help for the title</span></span>',
            $body
        );
        $this->assertStringContainsString('<div class="message info ">Boxed help text</div>', $body);

        # What InlineInfoField.js looks the target label up by
        $this->assertMatchesRegularExpression('#<div id="Form_EditForm_Title_Holder"[^>]*>\s*<label#', $body);
    }
}
