# Changelog

## 3.0.0 (unreleased)

Silverstripe 5 and 6. Silverstripe 4 is dropped, which is why this is a major release. See
[UPGRADING.md](UPGRADING.md).

### Breaking

- **Silverstripe 4 is no longer supported.** `silverstripe/framework` is now `^5 || ^6` (was
  `^4.4`), `silverstripe/vendor-plugin` `^2 || ^3` (was `^1.0`), and PHP `^8.1` (was not declared).
  Silverstripe 4 projects keep resolving the `2.x` tags.
- **`InfoField` now boxes object content too.** Content passed as an object (for example a
  `DBHTMLText` from `DBField::create_field()` or `renderWith()`) used to be output bare, without the
  info box; it is now wrapped in the same `div.message.info` as string content. If you relied on
  the bare output, use a plain `LiteralField` for that content.

### Fixed

- `InfoField` dropped its info box for object content (see above).
- `InlineInfoField` wrote the target field name into its `data-target` attribute unescaped; it is
  now attribute-escaped. The help text itself is still output as HTML, as documented.

### Added

- A behavioural test suite (`tests/`), run in CI against Silverstripe 5 and 6: the markup of both
  fields (the contract with the CMS script and stylesheet), the CMS requirements and their
  publication, and a real CMS page edit form.
- README: what the module does (the previous text described a different field), requirements,
  installation, a version compatibility table, usage of both fields, the assets it loads and where
  the inline field works.
- MIT licence (`LICENSE`, and `license` in `composer.json`); previously no licence was declared.
- `funding` and PSR-4 `autoload` in `composer.json`. Its description and keywords described a
  cookie notice and are corrected.

### Issues

- No issues or pull requests were open (or had ever been filed) at the time of this release.

## 2.1.1

- Remove a leftover `console.log` from the CMS script.
- (After the tag, on `master`: `silverstripe/vendor-plugin` added as a requirement.)

## 2.1

- Minor fixes.

## 2.0

- Silverstripe 4 vendor module.
