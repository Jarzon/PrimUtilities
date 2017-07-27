<?php
namespace Tests\Mock;

use PrimUtilities\Localization;

Class View extends \Prim\View {
    use Localization;

    public function __construct($root)
    {
        $this->messages = [
            'languages' => ['en', 'fr'],
            'test' => ['Translated test', 'Test']
        ];

        $this->setMessagesLanguage();
    }
}