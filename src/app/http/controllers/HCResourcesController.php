<?php namespace interactivesolutions\honeycombresources\app\http\controllers;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use interactivesolutions\honeycombcore\errors\facades\HCLog;
use interactivesolutions\honeycombcore\http\controllers\HCBaseController;
use interactivesolutions\honeycombresources\app\models\HCResources;
use interactivesolutions\honeycombresources\app\validators\HCResourcesValidator;

class HCResourcesController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminIndex ()
    {
        $config = [
            'title'       => trans ('HCResources::resources.page_title'),
            'listURL'     => route ('admin.api.resources'),
            'newFormUrl'  => route ('admin.api.form-manager', ['resources-new']),
            'editFormUrl' => route ('admin.api.form-manager', ['resources-edit']),
            'imagesUrl'   => route ('resource.get', ['/']),
            'headers'     => $this->getAdminListHeader (),
        ];

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_create'))
            $config['actions'][] = 'new';

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_update'))
            $config['actions'][] = 'restore';

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_delete'))
            $config['actions'][] = 'delete';

        $config['actions'][] = 'search';

        return view ('HCCoreUI::admin.content.list', ['config' => $config]);
    }

    /**
     * Creating Admin List Header based on Main Table
     *
     * @return array
     */
    public function getAdminListHeader ()
    {
        return [
            'original_name'    => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.original_name'),
            ],
            'id'               => [
                "type"  => "image",
                "label" => trans ('HCResources::resources.preview'),
            ],
            'size'             => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.size'),
            ],
            'path'             => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.path'),
            ],
            'preview_generate' => [
                "type"  => "silent-button",
                "label" => trans ('HCResources::resources.regenerate'),
                "url"   => route('admin.api.resources.regenerate', 'id'),
                "refresh" => true
            ],

        ];
    }

    /**
     * Show resource
     *
     * @param null|string $id
     * @param int|null $width
     * @param int|null $height
     * @param bool|null $fit
     * @return mixed
     */
    public function showResource (string $id = null, int $width = 0, int $height = 0, bool $fit = false)
    {
        $storagePath = storage_path ('app/');

        if (is_null ($id))
            return HCLog::error ('R-001', trans ('resources::resources.errors.resource_id_missing'));

        // cache resource for 10 days
        $resource = \Cache::remember($id, 14400, function () use ($id) {
            return HCResources::find ($id);
        });

        if (!$resource)
            return HCLog::error ('R-003', trans ('resources::resources.errors.resource_not_found'));

        if (!Storage::exists ($resource->path))
            HCLog::stop (trans ('resources::resources.errors.resource_not_found_in_storage') . ' : ' . $id);

        $cachePath = generateResourceCacheLocation ($resource->id, $width, $height, $fit) . $resource->extension;

        if (file_exists ($cachePath)) {
            $resource->size = File::size ($cachePath);
            $resource->path = $cachePath;
        } else {

            switch ($resource->mime_type) {
                case 'image/png' :
                case 'image/jpeg' :
                case 'image/jpg' :

                    if ($width != 0 && $height != 0) {

                        createImage ($storagePath . $resource->path, $cachePath, $width, $height, $fit);

                        $resource->size = File::size ($cachePath);
                        $resource->path = $cachePath;
                    } else
                        $resource->path = $storagePath . $resource->path;
                    break;

                case 'video/mp4' :

                    $previewPath     = str_replace ('-', '/', $resource->id);
                    $fullPreviewPath = $storagePath . 'video-previews/' . $previewPath;

                    $cachePath = generateResourceCacheLocation ($previewPath, $width, $height, $fit) . '.jpg';

                    if (file_exists ($cachePath)) {
                        $resource->size      = File::size ($cachePath);
                        $resource->path      = $cachePath;
                        $resource->mime_type = 'image/jpg';
                    } else {

                        if ($width != 0 && $height != 0) {

                            $videoPreview = $fullPreviewPath . '/preview_frame.jpg';

                            //TODO: generate 3-5 previews and take the one with largest size
                            $this->generateVideoPreview ($resource, $storagePath, $previewPath);

                            createImage ($videoPreview, $cachePath, $width, $height, $fit);

                            $resource->size      = File::size ($cachePath);
                            $resource->path      = $cachePath;
                            $resource->mime_type = 'image/jpg';
                        } else
                            $resource->path = $storagePath . $resource->path;
                    }
                    break;

                default:

                    $resource->path = $storagePath . $resource->path;
                    break;
            }
        }

        // Show resource
        header ('Pragma: public');
        header ('Cache-Control: max-age=86400');
        header ('Expires: ' . gmdate ('D, d M Y H:i:s \G\M\T', time () + 86400));
        header ('Content-Length: ' . $resource->size);
        header ('Content-Disposition: inline;filename="' . $resource->original_name . '"');
        header ('Content-Type: ' . $resource->mime_type);
        readfile ($resource->path);

        exit;
    }

    /**
     * generating video preview
     *
     * @param HCResources $resource
     * @param string $storagePath
     * @param string $previewPath
     */
    private function generateVideoPreview (HCResources $resource, string $storagePath, string $previewPath)
    {
        $fullPreviewPath = $storagePath . 'video-previews/' . $previewPath;

        if (!file_exists ($fullPreviewPath))
            mkdir ($fullPreviewPath, 0755, true);

        $videoPreview = $fullPreviewPath . '/preview_frame.jpg';

        if (!file_exists ($videoPreview)) {

            $videoPath = $storagePath . $resource->path;

            $ffmpeg = FFMpeg::create ([
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout'          => 3600, // The timeout for the underlying process
                'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ]);

            $video    = $ffmpeg->open ($storagePath . $resource->path);
            $duration = $video->getFFProbe ()->format ($videoPath)->get ('duration');

            $video->frame (TimeCode::fromSeconds (rand (1, $duration)))
                ->save ($videoPreview);

            $resource->mime_type = 'image/jpg';
            $resource->path      = $videoPreview;
        }
    }

    /**
     * Regenerating video preview
     *
     * @param string $id
     * @return array
     */
    public function regenerateVideoPreview (string $id)
    {
        $resource = HCResources::findOrFail ($id);

        //TODO check if resource is video

        $storagePath = storage_path ('app/');
        $previewPath = str_replace ('-', '/', $resource->id);

        if (file_exists($storagePath . 'video-previews/'. $previewPath))
            removeDirectory ($storagePath . 'video-previews/'. $previewPath);

        if (file_exists($storagePath . 'cache/' . $previewPath))
            removeDirectory ($storagePath  . 'cache/' . $previewPath);

        $this->generateVideoPreview ($resource, $storagePath, $previewPath);

        return ["success" => true];
    }

    /**
     * Function for streaming resource
     *
     * @param string $id
     */
    public function streamResource (string $id)
    {
        $stream = new HCVideoStream($id);
        $stream->start ();
    }

    /**
     * Create item
     *
     * @param array|null $data
     * @return mixed
     * @throws \Exception
     */
    protected function __apiStore (array $data = null)
    {
        if (is_null ($data)) {
            $resource = request ()->file ('file');
        } else {
            $resource = $data;
        }

        if ($resource == null)
            throw new \Exception(trans ('HCResources::resources.file_missing'));

        $uploadController = new HCUploadController();

        return $uploadController->upload ($resource);
    }

    /**
     * Updates existing item based on ID
     *
     * @param $id
     * @return mixed
     */
    protected function __apiUpdate (string $id)
    {
        $record = HCResources::findOrFail ($id);

        $data = $this->getInputData ();

        $record->update (array_get ($data, 'record'));

        return $this->apiShow ($record->id);
    }

    /**
     * Getting user data on POST call
     *
     * @return mixed
     */
    protected function getInputData ()
    {
        (new HCResourcesValidator())->validateForm ();

        $_data = request ()->all ();

        array_set ($data, 'record.original_name', array_get ($_data, 'original_name'));
        array_set ($data, 'record.safe_name', array_get ($_data, 'safe_name'));
        array_set ($data, 'record.size', array_get ($_data, 'size'));
        array_set ($data, 'record.path', array_get ($_data, 'path'));

        return $data;
    }

    /**
     * Getting single record
     *
     * @param $id
     * @return mixed
     */
    public function apiShow (string $id)
    {
        $with = [];

        $select = HCResources::getFillableFields ();

        $record = HCResources::with ($with)
            ->select ($select)
            ->where ('id', $id)
            ->firstOrFail ();

        return $record;
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed
     */
    protected function __apiDestroy (array $list)
    {
        HCResources::destroy ($list);

        return hcSuccess();
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed
     */
    protected function __apiForceDelete (array $list)
    {
        HCResources::onlyTrashed ()->whereIn ('id', $list)->forceDelete ();

        return hcSuccess();
    }

    /**
     * Restore multiple records
     *
     * @param $list
     * @return mixed
     */
    protected function __apiRestore (array $list)
    {
        HCResources::whereIn ('id', $list)->restore ();

        return hcSuccess();
    }

    /**
     * Creating data query
     *
     * @param array $select
     * @return mixed
     */
    protected function createQuery (array $select = null)
    {
        $with = [];

        if ($select == null)
            $select = HCResources::getFillableFields ();

        $list = HCResources::with ($with)->select ($select)
            // add filters
            ->where (function ($query) use ($select) {
                $query = $this->getRequestParameters ($query, $select);
            });

        // enabling check for deleted
        $list = $this->checkForDeleted ($list);

        // add search items
        $list = $this->search ($list);

        // ordering data
        $list = $this->orderData ($list, $select);

        return $list;
    }

    /**
     * List search elements
     * @param Builder $query
     * @param string $phrase
     * @return Builder
     */
    protected function searchQuery (Builder $query, string $phrase)
    {
        return $query->where (function (Builder $query) use ($phrase) {
            $query->where ('original_name', 'LIKE', '%' . $phrase . '%')
                ->orWhere ('size', 'LIKE', '%' . $phrase . '%')
                ->orWhere ('original_name', 'LIKE', '%' . $phrase . '%')
                ->orWhere ('path', 'LIKE', '%' . $phrase . '%');
        });
    }
}
