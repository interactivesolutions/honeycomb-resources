<?php

namespace interactivesolutions\honeycombresources\app\http\controllers;

use DB;
use HCLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use interactivesolutions\honeycombresources\app\models\HCResources;
use Ramsey\Uuid\Uuid;

class HCUploadController
{
    /**
     * File upload location
     *
     * @var string
     */
    private $uploadPath;

    /**
     * If uploaded file has predefined ID it will be used
     *
     * @var
     */
    private $resourceID;

    public function __construct()
    {
        $this->uploadPath = 'uploads/' . date("Y-m-d") . DIRECTORY_SEPARATOR;
    }

    /**
     * Upload and insert new resource into database
     * Catch for errors and if is error throw error
     *
     * @param UploadedFile $file
     * @param bool $full
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function upload(UploadedFile $file, bool $full = null, string $id = null)
    {
        if (is_null($file))
            return HCLog::info('R-UPLOAD-001', trans('resources::resources.errors.no_resource_selected'));

        DB::beginTransaction();

        $this->resourceID = $id;

        try {
            $resource = $this->createResourceRecord($file);
            $this->saveResourceInStorage($resource, $file);

            Artisan::call('hc:generate-thumbs', ['id' => $resource->id]);
        } catch (\Exception $e) {
            DB::rollback();

            if (isset($resource)) {
                $this->removeImageFromStorage($resource);
            }

            throw new \Exception($e);
        }

        DB::commit();

        if ($full)
            return $resource->toArray();

        return [
            'id'  => $resource->id,
            'url' => route('resource.get', $resource->id),
        ];
    }

    /**
     * Create resource record in database
     *
     * @param $file
     * @return HCResources
     */
    protected function createResourceRecord(UploadedFile $file)
    {
        return HCResources::create(
            $this->getFileParams($file)
        );
    }

    /**
     * Get file params
     *
     * @param $file
     * @return array
     */
    public function getFileParams(UploadedFile $file)
    {
        $params = [];

        if ($this->resourceID)
            $params['id'] = $this->resourceID;
        else
            $params['id'] = Uuid::uuid4();

        $params['original_name'] = $file->getClientOriginalName();
        $params['extension'] = '.' . $file->getClientOriginalExtension();
        $params['path'] = $this->uploadPath . $params['id'] . $params['extension'];
        $params['size'] = $file->getClientSize();
        $params['mime_type'] = $file->getClientMimeType();

        return $params;
    }

    /**
     * Upload file to server
     *
     * @param $resource
     * @param $file
     * @return mixed
     */
    protected function saveResourceInStorage(HCResources $resource, UploadedFile $file)
    {
        $this->createFolder($this->uploadPath);
        $file->move(storage_path('app/' . $this->uploadPath), $resource->id . '.' . $file->getClientOriginalExtension());
    }

    /**
     * Create folder
     * @param $path
     */
    protected function createFolder(string $path)
    {
        if (!Storage::exists($path))
            Storage::makeDirectory($path);
    }

    /**
     * Remove item from storage
     *
     * @param $resource
     */
    protected function removeImageFromStorage(HCResources $resource)
    {
        $path = $this->uploadPath . $resource->id;

        if (Storage::has($path)) {
            Storage::delete($path);
        }
    }

    /**
     * Downloading and storing image in the system
     *
     * @param string $source
     * @param bool $full - if set to true than return full resource data
     * @param string $id
     * @param null|string $mime_type
     * @return mixed
     */
    public function downloadResource(string $source, bool $full = null, string $id = null, string $mime_type = null)
    {
        $this->createFolder('uploads/tmp');

        $fileName = $this->getFileName($source);

        if ($fileName && $fileName != '') {

            $destination = storage_path('app/uploads/tmp/' . $fileName);

            file_put_contents($destination, file_get_contents($source));

            if (!\File::exists($destination)) {
                return null;
            }

            if (!$mime_type)
                $mime_type = mime_content_type($destination);

            $file = new UploadedFile($destination, $fileName, $mime_type, filesize($destination), null, true);

            return $this->upload($file, $full, $id);
        }
        
        return null;
    }

    /**
     * Retrieving file name
     *
     * @param $fileName
     * @return mixed
     */
    protected function getFileName(string $fileName)
    {
        if (!$fileName && filter_var($fileName, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $explodeFileURL = explode('/', $fileName);
        $fileName = end($explodeFileURL);

        return sanitizeString(pathinfo($fileName, PATHINFO_FILENAME)) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    }
}