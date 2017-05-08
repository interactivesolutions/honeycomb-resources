<?php namespace interactivesolutions\honeycombresources\app\http\controllers;

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
    public function adminView()
    {
        $config = [
            'title'       => trans('HCResources::resources.page_title'),
            'listURL'     => route('admin.api.resources'),
            'newFormUrl'  => route('admin.api.form-manager', ['resources-new']),
            'editFormUrl' => route('admin.api.form-manager', ['resources-edit']),
            'imagesUrl'   => route('resource.get', ['/']),
            'headers'     => $this->getAdminListHeader(),
        ];

        if ($this->user()->can('interactivesolutions_honeycomb_resources_resources_create'))
            $config['actions'][] = 'new';

        if ($this->user()->can('interactivesolutions_honeycomb_resources_resources_update')) {
            $config['actions'][] = 'update';
            $config['actions'][] = 'restore';
        }

        if ($this->user()->can('interactivesolutions_honeycomb_resources_resources_delete'))
            $config['actions'][] = 'delete';

        if ($this->user()->can('interactivesolutions_honeycomb_resources_resources_search'))
            $config['actions'][] = 'search';

        return view('HCCoreUI::admin.content.list', ['config' => $config]);
    }

    /**
     * Creating Admin List Header based on Main Table
     *
     * @return array
     */
    public function getAdminListHeader()
    {
        return [
            'original_name' => [
                "type"  => "text",
                "label" => trans('HCResources::resources.original_name'),
            ],
            'id'         => [
                "type"    => "image",
                "label"   => trans('HCResources::resources.dsaflhsalk'),
                "options" => [
                    "w" => 100,
                    "h" => 100
                ]
            ],
            'size'          => [
                "type"  => "text",
                "label" => trans('HCResources::resources.size'),
            ],
            'path'          => [
                "type"  => "text",
                "label" => trans('HCResources::resources.path'),
            ],

        ];
    }

    /**
     * Create item
     *
     * @param array|null $data
     * @return mixed
     * @throws \Exception
     */
    protected function __create(array $data = null)
    {
        if (is_null($data)) {
            $resource = request()->file('file');
        } else {
            $resource = $data;
        }

        if ($resource == null)
            throw new \Exception(trans('HCResources::resources.file_missing'));

        $uploadController = new HCUploadController();
        return $uploadController->upload($resource);
    }

    /**
     * Updates existing item based on ID
     *
     * @param $id
     * @return mixed
     */
    protected function __update(string $id)
    {
        $record = HCResources::findOrFail($id);

        $data = $this->getInputData();

        $record->update(array_get($data, 'record'));

        return $this->getSingleRecord($record->id);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed|void
     */
    protected function __delete(array $list)
    {
        HCResources::destroy($list);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed|void
     */
    protected function __forceDelete(array $list)
    {
        HCResources::onlyTrashed()->whereIn('id', $list)->forceDelete();
    }

    /**
     * Restore multiple records
     *
     * @param $list
     * @return mixed|void
     */
    protected function __restore(array $list)
    {
        HCResources::whereIn('id', $list)->restore();
    }

    /**
     * Creating data query
     *
     * @param array $select
     * @return mixed
     */
    public function createQuery(array $select = null)
    {
        $with = [];

        if ($select == null)
            $select = HCResources::getFillableFields();

        $list = HCResources::with($with)->select($select)
            // add filters
            ->where(function ($query) use ($select) {
                $query = $this->getRequestParameters($query, $select);
            });

        // enabling check for deleted
        $list = $this->checkForDeleted($list);

        // add search items
        $list = $this->listSearch($list);

        // ordering data
        $list = $this->orderData($list, $select);

        return $list;
    }


    /**
     * List search elements
     * @param $list
     * @return mixed
     */
    protected function listSearch(Builder $list)
    {
        if (request()->has('q')) {
            $parameter = request()->input('q');

            $list = $list->where(function ($query) use ($parameter) {
                $query->where('original_name', 'LIKE', '%' . $parameter . '%')
                    ->orWhere('size', 'LIKE', '%' . $parameter . '%')
                    ->orWhere('path', 'LIKE', '%' . $parameter . '%');
            });
        }

        return $list;
    }

    /**
     * Getting user data on POST call
     *
     * @return mixed
     */
    protected function getInputData()
    {
        (new HCResourcesValidator())->validateForm();

        $_data = request()->all();

        array_set($data, 'record.original_name', array_get($_data, 'original_name'));
        array_set($data, 'record.safe_name', array_get($_data, 'safe_name'));
        array_set($data, 'record.size', array_get($_data, 'size'));
        array_set($data, 'record.path', array_get($_data, 'path'));

        return $data;
    }

    /**
     * Getting single record
     *
     * @param $id
     * @return mixed
     */
    public function getSingleRecord(string $id)
    {
        $with = [];

        $select = HCResources::getFillableFields();

        $record = HCResources::with($with)
            ->select($select)
            ->where('id', $id)
            ->firstOrFail();

        return $record;
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
    public function showResource(string $id = null, int $width = 0, int $height = 0, bool $fit = false)
    {
        $storagePath = storage_path('app/');

        if (is_null($id))
            return HCLog::error('R-001', trans('resources::resources.errors.resource_id_missing'));

        $resource = HCResources::find($id);

        if (!$resource)
            return HCLog::error('R-003', trans('resources::resources.errors.resource_not_found'));

        if (!Storage::exists($resource->path))
            HCLog::stop(trans('resources::resources.errors.resource_not_found_in_storage') . ' : ' . $id);

        $cachePath = generateResourceCacheLocation($resource->id, $width, $height, $fit) . $resource->extension;

        if (file_exists($cachePath)) {
            $resource->size = File::size($cachePath);
            $resource->path = $cachePath;
        } else {

            switch ($resource->mime_type) {
                case 'image/png' :
                case 'image/jpeg' :
                case 'image/jpg' :

                    if ($width != 0 && $height != 0) {

                        createImage($storagePath . $resource->path, $cachePath, $width, $height, $fit);

                        $resource->size = File::size($cachePath);
                        $resource->path = $cachePath;
                    } else
                        $resource->path = $storagePath . $resource->path;
                    break;

                default:

                    $resource->path = $storagePath . $resource->path;
                    break;
            }
        }

        // Show resource
        header('Pragma: public');
        header('Cache-Control: max-age=86400');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header('Content-Length: ' . $resource->size);
        header('Content-Disposition: inline;filename="' . $resource->original_name . '"');
        header('Content-Type: ' . $resource->mime_type);
        readfile($resource->path);

        exit;
    }
}
