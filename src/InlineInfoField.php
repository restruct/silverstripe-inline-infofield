<?php

namespace Restruct\InfoField {

    use SilverStripe\Core\Convert;
    use SilverStripe\Forms\DatalessField;

    class InlineInfoField extends DatalessField
    {

        protected $targetField;

        protected $content;

        /**
         * Create a new HelpField.
         *
         * @param string $targetField The name of the form field to attach the help text to (eg, "MenuTitle" or
         *                            "Content")
         * @param string $content     The text/HTML contents of the help box
         */
        function __construct($targetField, $content)
        {
            $this->targetField = $targetField;
            $this->content = $content;

            parent::__construct($targetField . '_InlineInfoField', $content);
        }

        function FieldHolder($properties = [])
        {
//		self::include_requirements();

            # The target is a field name, but it lands in an HTML attribute: escape it so a quote
            # cannot break out of data-target. The content is HTML by contract and stays raw.
            return '<span class="message info small inline-info" data-target="' . Convert::raw2att($this->targetField) .
                '"><span>' . $this->content . '</span></span>';
        }

        function Field($properties = [])
        {
            return $this->FieldHolder($properties);
        }

        public static function get_module_dir()
        {
            return basename(dirname(__DIR__));
        }
    }
}
