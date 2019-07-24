<?php

namespace ColorIjo\Element;

/**
 *
 */
class Element
{

    public function __construct()
    {
        // code...
    }

    public function test()
    {
        return 'success';
    }

    public function view($value='')
    {
        // code...
    }

    public function addFormField($handler)
    {
        if (!$handler instanceof HandlerInterface) {
            $handler = app($handler);
        }

        $this->formFields[$handler->getCodename()] = $handler;

    }
}
