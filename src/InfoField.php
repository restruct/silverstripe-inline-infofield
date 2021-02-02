<?php

namespace Restruct\InfoField {

    use SilverStripe\Forms\LiteralField;

    /**
     * This field lets you put an arbitrary piece of HTML into your forms, contained in a styled info-box.
     *
     * <b>Usage</b>
     *
     * <code>
     * new InfoField (
     *    $name = "infofield",
     *    $content = '<b>some bold text</b> and <a href="http://silverstripe.com">a link</a>'
     * )
     * </code>
     */
    class InfoField extends LiteralField
    {

        public function FieldHolder($properties = [])
        {
            if ( is_object($this->content) ) {
                $obj = $this->content;
                if ( $properties )
                    $obj = $obj->customise($properties);

                return $obj->forTemplate();
            }

            $classes = '';
            if ( $this->extraClasses ) $classes = implode(' ', $this->extraClasses);

            return "<div class=\"message info $classes\">" . $this->content . '</div>';
        }

    }
}
