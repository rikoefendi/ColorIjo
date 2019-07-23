<?php

namespace ColorIjo\MediaManager\Http;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use ColorIjo\MediaManager\Model;
use Illuminate\Support\Facades\Crypt;
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

    public function list(Request $request)
    {
        $fields = $request->field ? explode(',',$request->field) : ['name', 'id', 'mime', 'unique'];
        if($request->field){
            $fieds = explode(',', $request->field);
        }
        $data = Model::select($fields)->orderBy('name', 'ASC')->paginate(4)->toArray();
        $callback = [];
        foreach ($data['data'] as $d) {
            if($request->size){
                $sizes = explode(',', $request->size);
                foreach ($sizes as $size) {
                    $d['url'][$size] = env('APP_URL').'/media/d/file/'.$d['unique'].'/'.$size.'.jpg';
                }
            }
            $d['url']['thumb'] =  env('APP_URL').'/media/d/file/'.$d['unique'].'/default.jpg';
            $callback[] = $d;
        }
        $data['data'] = collect($callback);
        return $data;
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
        $mime = $file->getMimeType();
        $name = $file->getClientOriginalName();
        $realPath = $file->getRealPath();
        $title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        // $this->resizeImage($file);
        // //check file is exists
        $isExist = Model::where('name', $name)->first();
        if($isExist){
            return response()->json([
                'msg' => 'File is exists!!!'
            ], 422);
        }
        $image = Image::make($realPath);
        $res = $this->resizeImage($file);
        $model = new Model();
        $model->name = $name;
        $model->mime = $mime;
        $model->title = $title;
        $model->alt = $title;
        $model->unique = $res['name'];
        $model->props = json_encode([
            'file' => $res['size'],
            'dimensions' => $image->width().'x'.$image->height(),
            'size' => $file->getSize()
        ]);
        if($res['size']){
            $model->save();
            return [
                'url' => ['thumb' => env('APP_URL').'/media/d/file/'.$model->unique.'/default.jpg'],
                'name' => $name,
                'mime' => $mime,
                'unique' => $model->unique,
                'id' => $model->id
            ];
        }
    }

    public function show($unique, Request $request)
    {
        $fields = $request->field ? explode(',',$request->field) : ['id', 'name', 'title', 'source', 'alt', 'mime', 'created_at', 'unique'];
        if($request->field){
            $fieds = explode(',', $request->field);
        }
        $data = Model::select($fields)->where('unique', $unique)->firstOrFail()->toArray();
        $path = config('medma.path').'/original/'.$data['unique'];
        if($request->size){
            $sizes = explode(',', $request->size);
            foreach ($sizes as $size) {
                $data['url'][$size] = env('APP_URL').'/media/d/file/'.$data['unique'].'/'.$size.'.jpg';
            }
        }

        $binary = base64_decode(Storage::get($path));
        // $data['url'] = env('APP_URL').'/media/d/file/'.\Crypt::encrypt($data['unique']).'/original';
        $data['size'] = Storage::size($path);
        if(preg_match("/(svg)+/", $data['mime'])){
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

    public function getImageLink($unique, $size){
        $model = Model::select('mime', 'name', 'unique')->where('unique', $unique)->firstOrFail();
        $sizes = array_keys(config('medma.size'));
        $sizes[] = 'maxresdefault';
        $sizes[] = 'default';
        $sizes[] = 'original';
        foreach ($sizes as $s) {
            if($s.'.jpg' == $size){
                return $this->getImage($model, $s);
            }
        }
        abort(404);
    }
    private function getImage($model, $size){

        $path = config('medma.path').'/'.$size.'/'.$model->unique;
        if(!Storage::exists($path)){
            abort(404);
        }
        $image = base64_decode(Storage::get($path));

        return response()->make($image, 200, [
                        'Content-Type' => $model->mime,
                        'Content-Disposition' => 'inline;filename="'.$model->name.'"',
                        'Cache-Control' => 'public, max-age=86400, no-transform'
                    ]);

    }
    public function destroy(Request $request)
    {
        $models = Model::whereIn('unique', $request->uniques)->get();
        //delete file
        $sizes = config('medma.size');
        $sizes[] = 'original';
        $sizes[] = 'default';
        $sizes[] = 'maxresdefault';
        foreach ($models as $model) {
            foreach ($sizes as $size => $res) {
                Storage::delete(config('medma.path').'/'.$size.'/'.$model->unique);
            }
            $model->delete();
        }

        return response()->json([
            'error' => false,
            'message' => 'Berhasil Menghapus image'
        ]);
    }

    private function resizeImage($file){
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
        $name = uniqid();
        $pathOri = config('medma.path').'/original/'.$name;
        $size = [];
        foreach ($sizes as $pathName => $res) {
            $siz = $this->makeFile($file, $pathName, $name, $res);
            if($siz){ $size[] = $siz;}
        }
        Storage::put($pathOri, base64_encode(file_get_contents($file->getRealPath())));
        return [
            'name' => $name,
            'size' => $size
        ];
    }

    private function makeFile($file, $pathName, $name, $res){
        $path = config('medma.path').'/'.$pathName.'/'.$name;
        $content = file_get_contents($file->getRealPath());
        $quality = config('medma.compress_quality');
        $encoded = '';
        $maxres = config('medma.path').'/maxresdefault/'.$name;
        $ext = $file->getClientOriginalExtension();
        $image = Image::make($content);
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

    public function getLinkDownload(Request $request)
    {
        $hash = Crypt::encrypt(uniqid());
        $unique = Crypt::encrypt($request->uniques);
        \Cache::put($unique, $hash, now()->addHours(12));
        return ['url' => env('APP_URL').'/download/'.$hash.'/'.$unique];
    }

    public function download($hash, $unique)
    {
        $chaceHash = \Cache::get($unique);
        $uniques = Crypt::decrypt($unique);
        $path = config('medma.path').'/original/';
        if(!$chaceHash || $hash != $chaceHash || !is_array($uniques)){
            return '!terlarang';
        }


        if(count($uniques) >= 2){
            $options = new \ZipStream\Option\Archive();
            $options->setSendHttpHeaders(true);
            return response()->stream(function() use ($uniques, $options, $path) {
                $data = Model::select('unique', 'name')->whereIn('unique',  $uniques)->get();
                $zip = new \ZipStream\ZipStream('medma-download-'.now().'.zip', $options);
                foreach ($data as $d) {
                    $file = Storage::get($path.$d->unique);
                    $zip->addFile($d->name, base64_decode($file));
                }
                $zip->finish();
            });
        }
        $model = Model::select('unique', 'name')->where('unique', $uniques)->first();
        return response()->make(base64_decode(Storage::get($path.$model->unique)), 200, [
                        'Content-Type' => $model->mime,
                        'Content-Disposition' => 'attachment;filename="'.$model->name.'"',
                        'Cache-Control' => 'private, max-age=86400, no-transform'
                    ]);
    }
}
