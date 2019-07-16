<?php

namespace ColorIjo\MediaManager\Http;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use ColorIjo\MediaManager\Model;
use Hash;
/**
 *
 */
class Controller extends BaseController
{
    public function __construct()
    {
        Image::configure(array('driver' => 'imagick'));
    }
    public function index($useImage, $multiple, $alias = null)
    {
        return view('medma::index');
    }
    public function featured($useImage, $multiple, $alias = null)
    {
        return view('medma::featured');
    }

    public function list()
    {
        $data = Model::all('name', 'id', 'mime', 'unique');
        foreach ($data as $d) {
            $d['thumbnailUrl'] = env('APP_URL').'/media/d/file/'.$d['unique'].'?s=thumbnail';
        }
        return $data;
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
        $mime = $file->getMimeType();
        $name = $file->getClientOriginalName();
        $realPath = $file->getRealPath();
        $title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        $unique = uniqid();

        //check file is exists
        $isExist = Model::where('name', $name)->first();
        if($isExist){
            return response()->json([
                'msg' => 'File is exists!!!'
            ], 422);
        }
        Storage::put('/images/'.$unique, base64_encode(file_get_contents($realPath)));
        $model = new Model();
        $model->name = $name;
        $model->mime = $mime;
        $model->title = $title;
        $model->alt = $title;
        $model->unique = $unique;
        $model->save();
        return [
            'thumbnailUrl' => env('APP_URL').'/media/d/file/'.$unique.'?s=thumbnail',
            'name' => $name,
            'mime' => $mime,
            'unique' => $model->unique,
            'id' => $model->id
        ];
    }

    public function show($unique)
    {
        $data = Model::select('id', 'name', 'title', 'source', 'alt', 'mime', 'created_at', 'unique')->where('unique', $unique)->firstOrFail();
        $binary = base64_decode(Storage::get('images/'.$data['unique']));
        $data['url'] = env('APP_URL').'/media/d/file/'.$data['unique'];
        $data['size'] = Storage::size('images/'.$data['unique']);
        if(preg_match("/(svg)+/", $data->mime)){
            $xmlget = simplexml_load_string($binary);
            $xmlattributes = $xmlget->attributes();
            $width = (string) $xmlattributes->width;
            $height = (string) $xmlattributes->height;
            $data['dimensions'] = $width.' x '.$height;
        }else{
            $image = Image::make($binary);
            $data['dimensions'] = $image->width().' x '.$image->height();
        }
        return $data;
    }

    public function update(Request $request, $unique)
    {
        // a
        $model = Model::where('unique', $unique)->firstOrFail();
        $model->alt = $request->alt;
        $model->name = $request->title.str_replace($model->title, "", $model->name);
        $model->title = $request->title;
        $model->source = $request->source;
        $model->save();
        return response()->json([
            'error' => false,
            'message' => 'Berhasil menyimpan perubahan',
            'data' => $this->show($unique)
        ], 200);
    }

    public function destroy($unique)
    {
        $model = Model::where('unique', $unique)->firstOrFail();
        //delete file
        Storage::delete('images/'.$model->unique);

        $model->delete();

        return response()->json([
            'error' => false,
            'message' => 'Berhasil Menghapus image'
        ]);
    }

    public function getImage($unique, Request $request)
    {
        $model = Model::select('mime', 'unique', 'name')->where('unique', $unique)->first();
        if(preg_match("/(svg)+/", $model->mime)){
            $binary = base64_decode(Storage::get('images/'.$unique));
            // dd($binary);
            return response()->make($binary, 200, ['Content-Type' => $model->mime, 'Content-Disposition' => 'inline; filename="'.$model->name.'"', 'cache-control' => 'private, max-age=86400, no-transform']);
        }
        $img = Image::cache(function($image) use ($unique, $request, $model){
            $encoded = $image->make(base64_decode(Storage::get('images/'.$unique)));
            if($request->s){
                if($request->s == 'thumbnail'){
                    $image->fit(200, 190, function ($constrait) {
                        $constrait->upsize();
                    });
                }else{
                    $size = explode(',', $request->s);
                    if(count($size) != 2){
                        return 'height * width is required separated with coma!!!';
                    }
                    if(is_numeric($size[0]) && is_numeric($size[1])){
                        $image->resize($size[0], $size[1], function ($constrait) {
                            $constrait->upsize();
                        });
                    }
                }
            }
        });
        // dd(['Content-Disposition' => 'attachment; filename="'.$model->name.'"']);
        return response()->make($img, 200, ['Content-Type' => $model->mime, 'Content-Disposition' => 'inline; filename="'.$model->name.'"', 'cache-control' => 'private, max-age=86400, no-transform']);
    }
}
