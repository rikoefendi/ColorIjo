<?php

namespace ColorIjo\Element;

use ColorIjo\Element\Element;
use ColorIjo\Element\Facades\Element as ElementFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
/**
 *
 */
class ElementServiceProvider extends ServiceProvider
{

    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Voyager', ElementFacade::class);

        $this->app->singleton('element', function () {
            return new Element();
        });

        $this->loadHelpers();

    }

    public function boot()
    {
        // dd(__DIR__.'/resources/views', 'element');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'element');
        $this->registerFormFields();
    }

    protected function loadHelpers()
    {
            require_once (__DIR__.'/helpers.php');
    }

    protected function registerViewComposers()
    {
        // Register alerts
        View::composer('cig::*');
    }
    public function registerFormFields()
    {
        $formFields = [
            'text'
            // 'checkbox',
            // 'multiple_checkbox',
            // 'color',
            // 'date',
            // 'file',
            // 'image',
            // 'multiple_images',
            // 'media_picker',
            // 'number',
            // 'password',
            // 'radio_btn',
            // 'rich_text_box',
            // 'code_editor',
            // 'markdown_editor',
            // 'select_dropdown',
            // 'select_multiple',
            // 'text',
            // 'text_area',
            // 'time',
            // 'timestamp',
            // 'hidden',
            // 'coordinates',
        ];

        foreach ($formFields as $formField) {
            $class = studly_case("{$formField}_handler");
            // dd($class);
            ElementFacade::addFormField("ColorIjo\\Element\\Handler\\{$class}");
        }
    }
}
