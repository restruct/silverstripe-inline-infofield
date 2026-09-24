# Upgrading

## 2.x to 3.0

3.0 supports Silverstripe 5 and 6 on PHP 8.1+. On Silverstripe 4, keep a `^2` constraint:
Composer will not offer 3.0 there.

On Silverstripe 5 and 6, change your constraint to `^3`:

```bash
composer require restruct/silverstripe-inline-infofield:^3
```

Class names, the namespace (`Restruct\InfoField`), constructor arguments, the rendered markup and
the CMS config are unchanged. Nothing to edit in `_config` or templates.

### Behaviour you may notice

- **`InfoField` with object content.** If you passed an object (a `DBHTMLText` from
  `DBField::create_field()` or `renderWith()`, for example) as the content, 2.x output it without
  the info box. 3.0 wraps it in the same `div.message.info` box as string content. If you wanted
  the bare output, use a `LiteralField` for that content instead.
- **`InlineInfoField` target names** are now attribute-escaped in `data-target`. For ordinary field
  names this changes nothing.
