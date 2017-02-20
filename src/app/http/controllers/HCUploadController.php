<?php

namespace interactivesolutions\honeycombresources\http\controllers;

use DB;
use HCLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use interactivesolutions\honeycombresources\models\HCResources;
use Ramsey\Uuid\Uuid;

class HCUploadController
{
    /**
     * File upload location
     *
     * @var string
     */
    private $uploadPath;

    public function __construct ()
    {
        $this->uploadPath = 'uploads/' . date ("Y-m-d") . DIRECTORY_SEPARATOR;
    }

    /**
     * Upload and insert new resource into database
     * Catch for errors and if is error throw error
     *
     * @param $file
     * @return array
     */
    public function upload (UploadedFile $file)
    {
        if (is_null ($file))
            return HCLog::info ('R-UPLOAD-001', trans ('resources::resources.errors.no_resource_selected'));

        DB::beginTransaction ();

        try {
            $resource = $this->createResourceRecord ($file);
            $this->saveResourceInStorage ($resource, $file);
        } catch (\Exception $e) {
            DB::rollback ();

            if (isset($resource)) {
                $this->removeImageFromStorage ($resource);
            }

            return HCLog::info ('R-UPLOAD-' . $e->getCode (), $e->getMessage ());
        }

        DB::commit ();

        return [
            'id'  => $resource->id,
            'url' => route ('resource.get', $resource->id),
        ];
    }

    /**
     * Create resource record in database
     *
     * @param $file
     * @return HCResources
     */
    protected function createResourceRecord (UploadedFile $file)
    {
        return HCResources::create (
            $this->getFileParams ($file)
        );
    }

    /**
     * Get file params
     *
     * @param $file
     * @return array
     */
    public function getFileParams (UploadedFile $file)
    {
        $params = [];

        $params['id'] = Uuid::uuid4 ();
        $params['original_name'] = $file->getClientOriginalName ();
        $params['path'] = $this->uploadPath;
        $params['size'] = $file->getClientSize ();

        return $params;
    }

    /**
     * Upload file to server
     *
     * @param $resource
     * @param $file
     * @return mixed
     */
    protected function saveResourceInStorage ($resource, UploadedFile $file)
    {
        $this->createFolder ($this->uploadPath);
        $file->move (storage_path ('app/' . $this->uploadPath), $resource->id . '.' . $file->getClientOriginalExtension ());
    }

    /**
     * Create folder
     * @param $path
     */
    protected function createFolder ($path)
    {
        if (!Storage::exists ($path))
            Storage::makeDirectory ($path);
    }

    /**
     * Remove item from storage
     *
     * @param $resource
     */
    protected function removeImageFromStorage ($resource)
    {
        $path = $this->uploadPath . $resource->id;

        if (Storage::has ($path)) {
            Storage::delete ($path);
        }
    }
}