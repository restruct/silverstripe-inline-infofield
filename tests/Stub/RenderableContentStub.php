<?php

namespace Restruct\InfoField\Tests\Stub;

use SilverStripe\Dev\TestOnly;

/**
 * Object content for InfoFieldTest whose forTemplate() and __toString() give DIFFERENT output, so a
 * test can tell whether InfoField rendered the object (forTemplate) or only cast it to a string.
 *
 * Deliberately NOT a ModelData/ViewableData/ArrayData subclass: those live in different namespaces
 * on Silverstripe 5 and 6, and a subclass would also pass an `instanceof ViewableData` check on
 * Silverstripe 5. A plain object with the two methods InfoField calls (customise, forTemplate) pins
 * the `is_object()` branch on both majors.
 */
class RenderableContentStub implements TestOnly
{
    # Values merged in by customise(), as ModelData::customise() would
    private array $customised = [];

    public function customise($data): static
    {
        $copy = clone $this;
        $copy->customised = array_merge($this->customised, (array) $data);
        return $copy;
    }

    public function forTemplate(): string
    {
        $extra = $this->customised ? ' ' . implode(',', array_map(
            fn ($key, $value) => "$key=$value",
            array_keys($this->customised),
            $this->customised
        )) : '';

        return '<i>rendered' . $extra . '</i>';
    }

    public function __toString(): string
    {
        # What a plain string cast (or string concatenation) of the object would produce
        return 'cast-to-string';
    }
}
