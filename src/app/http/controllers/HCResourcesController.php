<?php namespace interactivesolutions\honeycombresources\http\controllers;

use interactivesolutions\honeycombcore\http\controllers\HCBaseController;
use interactivesolutions\honeycombresources\models\HCResources;
use interactivesolutions\honeycombresources\validators\HCResourcesValidator;

class HCResourcesController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminView ()
    {
        $config = [
            'title'       => trans ('HCResources::resources.page_title'),
            'listURL'     => route ('admin.api.resources'),
            'newFormUrl'  => route ('admin.api.form-manager', ['resources-new']),
            'editFormUrl' => route ('admin.api.form-manager', ['resources-edit']),
            'imagesUrl'   => route ('resource.get', ['/']),
            'headers'     => $this->getAdminListHeader (),
        ];

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_create'))
            $config['actions'][] = 'new';

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_update')) {
            $config['actions'][] = 'update';
            $config['actions'][] = 'restore';
        }

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_delete'))
            $config['actions'][] = 'delete';

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_search'))
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
            'original_name' => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.original_name'),
            ],
            'safe_name'     => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.safe_name'),
            ],
            'size'          => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.size'),
            ],
            'path'          => [
                "type"  => "text",
                "label" => trans ('HCResources::resources.path'),
            ],

        ];
    }

    /**
     * Create item
     *
     * @param null $data
     * @return mixed
     */
    protected function __create ($data = null)
    {
        if (is_null ($data))
            $data = $this->getInputData ();

        $record = HCResources::create (array_get ($data, 'record'));

        return $this->getSingleRecord ($record->id);
    }

    /**
     * Updates existing item based on ID
     *
     * @param $id
     * @return mixed
     */
    protected function __update ($id)
    {
        $record = HCResources::findOrFail ($id);

        $data = $this->getInputData ();

        $record->update (array_get ($data, 'record'));

        return $this->getSingleRecord ($record->id);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed|void
     */
    protected function __delete (array $list)
    {
        HCResources::destroy ($list);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed|void
     */
    protected function __forceDelete (array $list)
    {
        HCResources::onlyTrashed ()->whereIn ('id', $list)->forceDelete ();
    }

    /**
     * Restore multiple records
     *
     * @param $list
     * @return mixed|void
     */
    protected function __restore (array $list)
    {
        HCResources::whereIn ('id', $list)->restore ();
    }

    /**
     * @return mixed
     */
    public function listData ()
    {
        $with = [];
        $select = HCResources::getFillableFields ();

        $list = HCResources::with ($with)->select ($select)
            // add filters
            ->where (function ($query) use ($select) {
                $query = where ($this->getRequestParameters ($query, $select));
            });

        // enabling check for deleted
        $list = $this->checkForDeleted ($list);

        // add search items
        $list = $this->listSearch ($list);

        // ordering data
        $list = $this->orderData ($list, $select);

        return $list->paginate ($this->recordsPerPage)->toArray ();
    }

    /**
     * List search elements
     * @param $list
     * @return mixed
     */
    protected function listSearch ($list)
    {
        if (request ()->has ('q')) {
            $parameter = request ()->input ('q');

            $list = $list->where (function ($query) use ($parameter) {
                $query->where ('original_name', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('safe_name', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('size', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('path', 'LIKE', '%' . $parameter . '%');
            });
        }

        return $list;
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
    public function getSingleRecord ($id)
    {
        $with = [];

        $select = HCResources::getFillableFields ();

        $record = HCResources::with ($with)
            ->select ($select)
            ->where ('id', $id)
            ->firstOrFail ();

        return $record;
    }
}
