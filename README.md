Silverstripe Inline Info Field
==============================

*Maintained by [Restruct](https://github.com/restruct). If this module saves you time, you can
[support ongoing maintenance](https://github.com/sponsors/restruct).*

Two form fields for putting help text in CMS forms:

* **`InfoField`** - a block of text or HTML in a styled info box (an info icon on a light
  background), between the other fields of a form.
* **`InlineInfoField`** - a small info icon next to **another field's label**. Its text stays hidden
  until the editor hovers over the icon (or taps it, on a touch device), so it adds instructions
  without taking up space.

Requirements
------------

* Silverstripe 5 or 6 (`silverstripe/framework`)
* PHP 8.1 or newer
* The CMS admin (`silverstripe/admin`) for the styling and the inline behaviour: the module's script
  and stylesheet are loaded on CMS screens only (see [Assets](#assets)).

Installation
------------

```
composer require restruct/silverstripe-inline-infofield
```

Version compatibility
---------------------

| Branch | Module version | Silverstripe | PHP |
|--------|----------------|--------------|-----|
| `master` | `3.x` | `^5 \|\| ^6` | `^8.1` |
| (tags only) | `2.0` - `2.1.1` | `^4.4` | not declared |

Silverstripe 4 reached end of life in April 2025 and is no longer supported or tested here. Projects
still on it should stay on the `2.x` tags, which remain available.

The default branch is the only maintained line: it supports every Silverstripe version this module
still targets, so there is no separate maintenance branch. A version branch will be created only when
a change cannot be made compatible across the supported range.

**`composer.json` is the source of truth** for exact constraints; this table is a quick reference.

Usage
-----

Both fields live in the `Restruct\InfoField` namespace.

### InfoField

```php
use Restruct\InfoField\InfoField;

$fields->addFieldToTab(
    'Root.Main',
    InfoField::create('LayoutInfo', 'Save the page first, then edit the <b>blocks</b> from their tabs.'),
    'Title' // optional: insert before this field
);
```

* **`InfoField::create($name, $content)`** - `$name` is only an identifier (the field holds no
  data); `$content` is a string of HTML, or an object such as a `DBHTMLText` from
  `DBField::create_field()` or `renderWith()`.
* Renders `<div class="message info ...">$content</div>`. Classes added with `addExtraClass()` are
  appended to the box.
* **The content is output as HTML, unescaped.** Escape any user-supplied text yourself, for example
  with `Convert::raw2xml()`.

`InfoField` extends `LiteralField`, so everything else a `LiteralField` offers (`setContent()`,
`getContent()`, readonly transformation) applies.

### InlineInfoField

```php
use Restruct\InfoField\InlineInfoField;

$fields->push(InlineInfoField::create('Title', 'Keep titles short: they are also used in the menu.'));
```

* **`InlineInfoField::create($targetField, $content)`** - `$targetField` is the **name of the field
  whose label gets the info icon** (for example `Title`, `MenuTitle` or `Content`); `$content` is the
  help text as a string of HTML, output unescaped.
* The field's own name is `<targetField>_InlineInfoField`, so use that name to remove or move it.
* Where you add it to the field list does not matter much: in the CMS its script moves the icon into
  the target field's label as soon as the form loads.
* It renders `<span class="message info small inline-info" data-target="<targetField>">`, with the
  help text in an inner `<span>` that the script shows on hover (or toggles on tap on a touch
  device).

**Which forms it works in.** The script finds the target by the holder id
`Form_EditForm_<targetField>_Holder`, or an id ending in that. This matches the CMS edit forms whose
form is named `EditForm`, such as the page editor. A GridField detail form (for example in a
`ModelAdmin`) has ids starting `Form_ItemEditForm_`, which the script does not match, so there the
icon is not moved into the label. Outside the CMS the script is not loaded at all (see below).

### Assets

The module adds its script and stylesheet to **every CMS screen** through `LeftAndMain` config
(`_config/config.yml`):

```yaml
SilverStripe\Admin\LeftAndMain:
  extra_requirements_javascript:
    - 'restruct/silverstripe-inline-infofield:client/dist/js/InlineInfoField.js'
  extra_requirements_css:
    - 'restruct/silverstripe-inline-infofield:client/dist/css/InlineInfoField.css'
```

Both are published by Composer from the directories listed under `extra.expose` in the module's
`composer.json`. On the front end nothing is loaded, so a front-end form that uses these fields must
include its own styling (and `InlineInfoField` stays where it was placed).

The stylesheet styles `.message.info` elements in general, not only the ones these fields output, so
other CMS messages with those classes also get the info box look.

`InlineInfoField::get_module_dir()` returns the module's directory name. It is not used by the module
itself and is kept for code that called it.

Running the tests
-----------------

The module cannot be tested on its own: it needs a host Silverstripe project (with
`silverstripe/recipe-cms` and `silverstripe/recipe-testing`). Require it there through a Composer
**path repository with `symlink: true`** - `/tests` is `export-ignore`, so a dist or mirrored install
contains no tests - add `Restruct\InfoField\Tests\` pointing at the module's `tests/` to the host's
`autoload-dev`, copy `phpunit.xml.dist` to the host root as `phpunit.xml`, then:

```bash
# Silverstripe 5 (PHPUnit 9) - the path must come before flush=1
vendor/bin/phpunit vendor/restruct/silverstripe-inline-infofield/tests flush=1

# Silverstripe 6 (PHPUnit 11) - a flush=1 argument is ignored, use the env var
SS_PHPUNIT_FLUSH=1 vendor/bin/phpunit --testsuite infofield
```

The suite checks the rendered markup, the CMS requirements and their publication, and renders a real
CMS page edit form. It does not run the script in a browser.

CI runs the same suite against Silverstripe 5 and 6 on every push; see `.github/workflows/ci.yml`.

Credits
-------

`InlineInfoField` is derived from
[nathancox/silverstripe-helpfield](https://github.com/nathancox/silverstripe-helpfield) by Nathan
Cox, which the 2.0 README credited.

License
-------

No licence is declared, as in every `2.x` release.
