<?php

namespace interactivesolutions\honeycombresources\http\controllers;

use DB;
use HCLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use interactivesolutions\honeycombresources\models\HCResources;
use phpDocumentor\Reflection\Types\Boolean;
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
     * @param UploadedFile $file
     * @param bool $full
     * @return mixed
     */
    public function upload (UploadedFile $file, bool $full = null)
    {
        if (is_null ($file))
            return HCLog::info ('R-UPLOAD-001', trans ('resources::resources.errors.no_resource_selected'));

        DB::beginTransaction ();

        try {
            $resource = $this->createResourceRecord ($file);
            $this->saveResourceInStorage ($resource, $file);

            Artisan::call ('hc:generate-thumbs', ['id' => $resource->id]);
        } catch (\Exception $e) {
            DB::rollback ();

            if (isset($resource)) {
                $this->removeImageFromStorage ($resource);
            }

            return HCLog::info ('R-UPLOAD-' . $e->getCode (), $e->getMessage ());
        }

        DB::commit ();

        if ($full)
            return $resource->toArray();

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
        $params['extension'] = '.' . $file->getClientOriginalExtension ();
        $params['path'] = $this->uploadPath . $params['id'] . $params['extension'];
        $params['size'] = $file->getClientSize ();
        $params['mime_type'] = $file->getClientMimeType ();

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

    /**
     * Downloading and storing image in the system
     *
     * @param $imageURL
     * @param bool $full - if set to true than return full resource data
     * @return mixed
     */
    public function downloadAndSaveImage ($imageURL, bool $full = null)
    {
        $this->createFolder ('uploads/tmp');

        $fileName = $this->getFileName ($imageURL);

        if ($fileName && $fileName != '') {
            $destination = storage_path ('app/uploads/tmp/' . $fileName);

            // download resource to tmp folder with wget
            echo (shell_exec (sprintf ('wget --quiet -O %s %s', escapeshellarg ($destination), escapeshellarg ($imageURL))));

            if (!\File::exists ($destination)) {
                return null;
            }

            $file = new UploadedFile($destination, $fileName, mime_content_type ($destination), filesize ($destination), null, true);

            return $this->upload ($file, $full);
        }

        return null;
    }

    /**
     * Retrieving file name
     *
     * @param $fileName
     * @return mixed
     */
    protected function getFileName ($fileName)
    {
        if (!$fileName && filter_var ($fileName, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $explodeFileURL = explode ('/', $fileName);
        $fileName = end ($explodeFileURL);

        return sanitizeString (pathinfo ($fileName, PATHINFO_FILENAME)) . '.' . pathinfo ($fileName, PATHINFO_EXTENSION);
    }
}