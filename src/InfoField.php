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
            # Object content (eg. a DBHTMLText from DBField::create_field() or renderWith()) is
            # rendered first and then boxed like string content. It used to be returned bare, as
            # LiteralField does, so the info box this class exists for silently went missing.
            $content = $this->content;
            if ( is_object($content) ) {
                $obj = $content;
                if ( $properties )
                    $obj = $obj->customise($properties);

//                return $obj->forTemplate();
                $content = $obj->forTemplate();
            }

            $classes = '';
            if ( $this->extraClasses ) $classes = implode(' ', $this->extraClasses);

            return "<div class=\"message info $classes\">" . $content . '</div>';
        }

    }
}
