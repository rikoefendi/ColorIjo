<?php

namespace ColorIjo\MediaManager\Listeners;

use ColorIjo\MediaManager\Events\FileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FileUploadListener implements ShouldQueue
{
    use InteractsWithQueue;
    private $parser = [
        'image' => '\ColorIjo\MediaManager\Parsers\Image',
    ];
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FileUploaded  $event
     * @return void
     */
    public function handle(FileUploaded $event)
    {
        //choose parser file
        $model = $event->model;
        foreach ($this->parser as $type => $parser) {
            if(preg_match("/($type)[\s\S]*/", $model->mime)){
                return new $parser($model);
            }
        }
    }
}
