<?php
namespace Tests\Mock;

Class Localization extends \PrimUtilities\Localization {
    public function __construct()
    {
        $this->messages = [
            'languages' => ['en', 'fr'],
            'test' => ['Translated test', 'Test traduit']
        ];

        $this->setMessagesLanguage();
    }
}