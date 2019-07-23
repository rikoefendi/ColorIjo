<?php

namespace ColorIjo\MediaManager;
use ColorIjo\MediaManager\Http\Controller;
/**
 *
 */
class MediaManager
{

    public function routes()
    {
        require_once(__DIR__.'/routes/web.php');
    }

    public function api(){
        require_once(__DIR__.'/routes/api.php');
    }
}
