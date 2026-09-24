<?php

namespace Restruct\InfoField\Tests;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Dev\SapphireTest;

/**
 * The module's JavaScript and CSS are added to every CMS screen through LeftAndMain config. Each
 * half can fail silently: without the config entry nothing loads, and without the matching
 * composer `expose` entry the CMS links a /_resources URL that was never published (a 404 with
 * nothing wrong in PHP). A resourceURL() assertion cannot catch the second, so the expose list
 * is checked directly.
 */
class CmsRequirementsTest extends SapphireTest
{
    private const MODULE = 'restruct/silverstripe-inline-infofield';

    protected function setUp(): void
    {
        parent::setUp();
        if (!class_exists(LeftAndMain::class)) {
            $this->markTestSkipped('silverstripe/admin is not installed');
        }
    }

    public function testScriptIsAddedToEveryCmsScreen()
    {
        $this->assertContains(
            self::MODULE . ':client/dist/js/InlineInfoField.js',
            LeftAndMain::config()->get('extra_requirements_javascript')
        );
    }

    public function testStylesheetIsAddedToEveryCmsScreen()
    {
        $this->assertContains(
            self::MODULE . ':client/dist/css/InlineInfoField.css',
            LeftAndMain::config()->get('extra_requirements_css')
        );
    }

    public function testEveryConfiguredResourceExistsAndIsExposed()
    {
        $resources = array_merge(
            (array) LeftAndMain::config()->get('extra_requirements_javascript'),
            (array) LeftAndMain::config()->get('extra_requirements_css')
        );
        $ours = array_filter($resources, fn ($r) => str_starts_with((string) $r, self::MODULE . ':'));
        $this->assertCount(2, $ours);

        foreach ($ours as $resource) {
            $path = ModuleResourceLoader::singleton()->resolvePath($resource);
            $this->assertFileExists(BASE_PATH . '/' . $path, "$resource does not resolve to a file");
            $this->assertExposed(substr($resource, strlen(self::MODULE) + 1));
        }
    }

    public function testImagesTheStylesheetReferencesExistAndAreExposed()
    {
        $module = ModuleLoader::getModule(self::MODULE);
        $cssDir = 'client/dist/css';
        $css = file_get_contents($module->getPath() . '/' . $cssDir . '/InlineInfoField.css');

        preg_match_all('/url\([\'"]?([^\'")]+)[\'"]?\)/', $css, $matches);
        $this->assertNotEmpty($matches[1], 'expected the stylesheet to reference its info icon');

        foreach ($matches[1] as $url) {
            # Resolve the url() relative to the stylesheet's own directory, as a browser does
            $parts = [];
            foreach (explode('/', $cssDir . '/' . $url) as $part) {
                if ($part === '..') {
                    array_pop($parts);
                } elseif ($part !== '.' && $part !== '') {
                    $parts[] = $part;
                }
            }
            $relative = implode('/', $parts);
            $this->assertFileExists($module->getPath() . '/' . $relative, "$url in the stylesheet does not exist");
            $this->assertExposed($relative);
        }
    }

    /**
     * Assert a module-relative file lies inside one of the directories composer.json exposes.
     */
    private function assertExposed(string $relative): void
    {
        $module = ModuleLoader::getModule(self::MODULE);
        $composer = json_decode(file_get_contents($module->getPath() . '/composer.json'), true);
        $exposed = $composer['extra']['expose'] ?? [];

        foreach ($exposed as $dir) {
            if (str_starts_with($relative, rtrim($dir, '/') . '/')) {
                $this->addToAssertionCount(1);
                return;
            }
        }
        $this->fail("$relative is not inside any extra.expose directory of composer.json");
    }
}
