<?php
namespace PrimUtilities;

/*
 * Trait used to inject localization code into the View
 * */
trait Localization
{
    public $language = 'en';
    static public $messagesLanguage = '';
    public $messages = [];

    function buildLocalization() {
        $this->setMessagesLanguage();
        $this->fetchTranslation();

        $this->registerFunction('_', function(string $message) {
            return $this->translate($message);
        });
    }

    function translate(string $message) : string
    {
        return (isset($this->messages[$message]))? $this->messages[$message][self::$messagesLanguage]: $message;
    }

    function currency(float $price) : string
    {
        return number_format($price, 2, ',', ' ') . ' $';
    }

    function getLanguage() : string
    {
        return $this->language;
    }

    function setLanguage(string $language)
    {
        $this->language = $language;
    }

    function setMessagesLanguage()
    {
        self::$messagesLanguage = array_search($this->language, $this->messages['languages']);
    }

    function fetchTranslation()
    {
        $file = $this->root . 'app/config/messages.json';

        // Check if we have a translation file for that language
        if (file_exists($file)) {
            // TODO: Cache the file
            $this->messages = json_decode(file_get_contents($file), true);
        }
    }
}