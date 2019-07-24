<?php

namespace ColorIjo\MediaManager\Parsers;
use Intervention\Image\ImageManagerStatic as Codec;
use Illuminate\Support\Facades\Storage;
/**
 *
 */


class Image
{

    private $model;
    public function __construct($model)
    {
        $this->model = $model;
        $this->parser();
        Codec::configure(array('driver' => 'imagick'));
    }
    public function parser()
    {
        $path = config('medma.path').'/original/'.$this->model->unique;
        $image = Codec::make(Storage::get($path));
        $this->resizeImage($image, $this->model->unique);
    }
    private function resizeImage($image, $name){
        $sizes = config('medma.size');
        $sizes['default'] = [
            'auto' => true,
            'w' => 200,
            'h' => 190
        ];
        $sizes['maxresdefault'] = [
            'auto' => true,
            'w' => 1280,
            'h' => 720
        ];
        $size = [];
        foreach ($sizes as $pathName => $res) {
            $siz = $this->makeFile($image, $pathName, $name, $res);
            if($siz){ $size[] = $siz;}
        }
        return [
            'name' => $name,
            'size' => $size
        ];
    }

    private function makeFile($image, $pathName, $name, $res){
        $path = config('medma.path').'/'.$pathName.'/'.$name;
        $quality = config('medma.compress_quality');
        $encoded = '';
        $maxres = config('medma.path').'/maxresdefault/'.$name;
        $ext = substr(strrchr($this->model->name,'.'),1);
        if($res['auto']){
            if($image->width() <= $image->height() && $image->height() >= $res['h'] && $pathName != 'default'){
                $encoded = $this->createCanvas($image, $res, 'landscape', $ext);
                Storage::put($path, $encoded);
                return $pathName;
            }elseif(!Storage::exists($path) && $pathName == 'default'){
                $encoded = $image->fit($res['w'], $res['h'], function($img){
                    $img->aspectRatio();
                })->encode($ext, config('medma.compress_quality'))->encoded;
                Storage::put($path, base64_encode($encoded));
                return $pathName;
            }elseif($image->width() <= $image->height() && $image->height() <= $res['h']){
                $encoded = $image->encode($ext, config('medma.compress_quality'))->encoded;
                if(!Storage::exists($maxres)){
                    Storage::put($maxres, base64_encode($encoded));
                    return 'maxresdefault';
                }
            }elseif($image->width() > $image->height() && $image->height() >= $res['w']){
                $encoded = $this->createCanvas($image, $res, 'potrait', $ext);
                Storage::put($path, $encoded);
                return $pathName;
            }elseif($image->width() > $image->height() && $image->height() <= $res['w']){
                $encoded = $image->encode($ext, config('medma.compress_quality'))->encoded;
                if(!Storage::exists($maxres)){
                    Storage::put($maxres, base64_encode($encoded));
                    return 'maxresdefault';
                }
            }
        }else{
            $encoded = $image->resize($res['w'], $res['h'])->encode($ext, config('medma.compress_quality'))->encoded;
            Storage::put(config('medma.path').'/'.$pathName.'/'.$name, base64_encode($encoded));
            return $pathName;
        }
    }
    private function createCanvas($image, $res, $rotate, $ext){
        if($rotate == 'landscape'){
            $image->resize(null, $res['h'], function($img){
                $img->aspectRatio();
            });
        }
        if($rotate == 'potrait'){
            $image->resize($res['w'], null, function($img){
                $img->aspectRatio();
            });
        }
        $encoded = Image::canvas($res['w'], $res['h']);
        if($ext != 'png'){
            $encoded->fill('#000');
        }
        return base64_encode($encoded->insert($image->encode($ext)->encoded, 'center')->encode($ext, config('medma.compress_quality'))->encoded);
    }
}
