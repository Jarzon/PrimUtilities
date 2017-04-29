<?php
namespace PrimUtilities;

trait Localization
{
    protected $language = 'en';
    static protected $messagesLanguage = '';
    protected $messages = [];

    function __construct()
    {
        $this->fetchTranslation();
    }

    function getLanguage() : string
    {
        return $this->language;
    }

    function setLanguage(string $language)
    {
        $this->language = $language;
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

    function currency(float $price) : string {
        return number_format($price, 2, ',', ' ') . ' $';
    }
}