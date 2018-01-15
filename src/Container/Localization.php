<?php
namespace PrimUtilities\Container;

trait Localization {
    /**
     * @return \PrimUtilities\Localization
     */
    public function getLocalizationService()
    {
        $obj = 'localizationService';

        $this->setDefaultParameter($obj, '\PrimUtilities\Localization');

        return $this->init($obj, $this->getView());
    }
}