<?php

namespace ColorIjo\MediaManager;

use Illuminate\Support\Facades\Facade as BaseFacade;

class MedmaFacade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'medma';
    }
}
