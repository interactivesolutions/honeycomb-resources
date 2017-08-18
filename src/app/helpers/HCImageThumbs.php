<?php

namespace interactivesolutions\honeycombresources\app\helpers;

use File;
use HCLog;
use Intervention\Image\ImageManagerStatic;
use interactivesolutions\honeycombresources\app\models\HCResources;
use interactivesolutions\honeycombresources\app\models\resources\HCThumbs;

class HCImageThumbs
{
    const THUMB_EXTENSION = '.jpg';

    /**
     * Thumbs path where images will be saved
     *
     * @var string
     */
    private $thumbsFolder;

    /**
     * Generate thumb by given params
     *
     * @param string $resourceId
     * @param string $thumbId
     * @param int $quality
     */
    public function generate(string $resourceId, string $thumbId, $quality = 100)
    {
        $setting = $this->getThumbSettings($thumbId);

        $this->setThumbFolderName($setting);

        $resource = $this->getResource($resourceId);

        $file = $this->getOriginalFile($resource);

        if( $file ) {
            $image = $this->resizeImage($file, $setting);
//            $image = $this->addWatermark($image, $setting);
            $image->encode('jpg', $quality);
            $image->save($this->getLocation($resource));
        }
    }

    /**
     * Create Image folder
     *
     * @param $path
     */
    protected function createThumbsFolder($path)
    {
        if( ! File::exists($path) )
            File::makeDirectory($path, 0775, true);
    }

    /**
     * Get file location
     *
     * @param $resource
     * @return string
     */
    protected function getLocation($resource)
    {
        return $this->getThumbsFolder() . $resource->id . self::THUMB_EXTENSION;
    }

    /**
     * Get original file instance
     *
     * @param $resource
     * @return mixed
     */
    protected function getOriginalFile($resource)
    {
        $file = null;
        $path = storage_path('app/' . $resource->path);

        if( ! File::exists($path) ) {
            HCLog::error('R-THUMB-001', 'File not found at path: ' . $path);
        } else if( $resource->isImage() || strpos($resource->mime_type, 'svg') !== false ) {
            $file = File::get($path);
        }

        return $file;
    }

    /**
     * Resize image by given settings
     *
     * @param $file
     * @param $setting
     * @return mixed
     */
    protected function resizeImage($file, $setting)
    {
        $image = ImageManagerStatic::make($file);

        if( $setting->fit === "1" ) {
            $image->fit($setting->width, $setting->height, function ($constraint) use ($setting) {
                $constraint->upsize();
            });
        } else {
            $image->resize($setting->width, $setting->height, function ($constraint) use ($setting) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $image;
    }

    /**
     * Get image thumb setting
     *
     * @param string $thumbId
     * @return mixed
     */
    protected function getThumbSettings(string $thumbId)
    {
        return HCThumbs::findOrFail($thumbId);
    }

    /**
     * Get resources
     *
     * @param string $resourceId
     * @return mixed
     */
    protected function getResource(string $resourceId)
    {
        return HCResources::findOrFail($resourceId);
    }

//    /**
//     * Add watermark
//     *
//     * @param $image
//     * @param $settings
//     * @return mixed
//     */
//    protected function addWatermark($image, $settings)
//    {
//        $watermark = $this->getWatermark($settings);
//
//        if( ! is_null($watermark) ) {
//            if( $image->width() > $watermark->minimum_width || $image->height() > $watermark->minimum_height ) {
//                $watermarkImage = $this->formWatermarkImage($image, $watermark);
//
//                // add watermark if watermark exists
//                if( ! is_null($watermarkImage) ) {
//                    $image->insert($watermarkImage, $watermark->position, $watermark->offset_x, $watermark->offset_y);
//                }
//            }
//        }
//
//        return $image;
//    }
//
//    /**
//     * Get watermark
//     *
//     * @param $settings
//     * @return mixed
//     */
//    private function getWatermark($settings)
//    {
//        return OCWatermarks::find($settings->watermark_id);
//    }
//
//    /**
//     * Form watermark image
//     *
//     * @param $image
//     * @param $watermark
//     * @return mixed
//     */
//    protected function formWatermarkImage($image, $watermark)
//    {
//        $watermarkImage = $watermark->resource()->first();
//
//        if( ! is_null($watermarkImage) ) {
//
//            $watermarkFile = $this->getOriginalFile($watermarkImage);
//
//            if( is_null($watermarkFile) ) {
//                $watermarkImage = null;
//            } else {
//                $watermarkImage = ImageManager::make($watermarkFile);
//
//                $wSize = $this->getWatermarkSize($image, $watermark);
//
//                $watermarkImage->resize($wSize[0], $wSize[1], function ($constraint) {
//                    $constraint->aspectRatio();
//                });
//            }
//        }
//
//        return $watermarkImage;
//    }
//
//    /**
//     * Get watermark size
//     *
//     * @param $image
//     * @param $watermark
//     * @return array ['width', 'height']
//     */
//    protected function getWatermarkSize($image, $watermark)
//    {
//        // if not isset watermark size than default value is 25%
//        $size = ($watermark->watermark_size > 0) ? $watermark->watermark_size : 25;
//
//        $watermarkWidth = ($image->width() * $size) / 100;
//        $watermarkHeight = ($image->height() * $size) / 100;
//
//        return [$watermarkWidth, $watermarkHeight];
//    }

    /**
     * Get thumb folder name
     *
     * @param $setting
     * @return string
     */
    protected function setThumbFolderName($setting)
    {
        $name = getThumbName($setting);

        $this->thumbsFolder  = storage_path('app/public/thumbs/' . $name . DIRECTORY_SEPARATOR);

        $this->createThumbsFolder($this->thumbsFolder);
    }

    /**
     * Get folder name
     *
     * @return string
     */
    public function getThumbsFolder()
    {
        return $this->thumbsFolder;
    }
}