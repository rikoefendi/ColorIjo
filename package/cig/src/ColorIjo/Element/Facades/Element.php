<?php

namespace ColorIjo\Element\Facades;

use Illuminate\Support\Facades\Facade;

class Element extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'element';
    }
}
