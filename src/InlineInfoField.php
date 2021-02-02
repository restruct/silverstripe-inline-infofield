<?php

namespace Restruct\InfoField {

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

            return '<span class="message info small inline-info" data-target="' . $this->targetField .
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
