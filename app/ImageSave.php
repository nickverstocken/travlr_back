<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 26/10/17
 * Time: 15:18
 */

namespace App;



use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
class ImageSave
{
private $width;
private $height;
private $folder;
private $filename;
private $image;
    public function __construct($width, $height, $folder, $filename, $image)
    {
        $this->width = $width;
        $this->height = $height;
        $this->folder = $folder;
        $this->filename = $filename;
        $this->image = $image;
    }
    public function saveImage(){
        $image = Image::make($this->image)->fit($this->width, $this->height);
        $image = $image->stream();
        Storage::disk('s3')->put($this->folder . '/' . $this->filename, $image->__toString());
        return Storage::disk('s3')->url($this->folder . '/' . $this->filename);
    }
}