<?php
namespace PrimUtilities\Container;

trait Localization {
    /**
     * @return \PrimUtilities\Localization
     */
    public function getLocalizationService()
    {
        $obj = 'localizationService';

        return $this->init($obj, $this->getView());
    }
}