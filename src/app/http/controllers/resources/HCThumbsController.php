<?php namespace interactivesolutions\honeycombresources\app\http\controllers\resources;

use Illuminate\Database\Eloquent\Builder;
use interactivesolutions\honeycombcore\http\controllers\HCBaseController;
use interactivesolutions\honeycombresources\app\models\HCResources;
use interactivesolutions\honeycombresources\app\models\resources\HCThumbs;
use interactivesolutions\honeycombresources\app\validators\resources\HCThumbsValidator;

class HCThumbsController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminView ()
    {
        $config = [
            'title'       => trans ('HCResources::resources_thumbs.page_title'),
            'listURL'     => route ('admin.api.resources.thumbs'),
            'newFormUrl'  => route ('admin.api.form-manager', ['resources-thumbs-new']),
            'editFormUrl' => route ('admin.api.form-manager', ['resources-thumbs-edit']),
        //    'imagesUrl'   => route ('resource.get', ['/']),
            'headers'     => $this->getAdminListHeader (),
        ];

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_create'))
            $config['actions'][] = 'new';

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_update')) {
            $config['actions'][] = 'update';
            $config['actions'][] = 'restore';
        }

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_delete'))
            $config['actions'][] = 'delete';

        if ($this->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_search'))
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
            'name'         => [
                "type"  => "text",
                "label" => trans ('HCResources::resources_thumbs.name'),
            ],
            'width'        => [
                "type"  => "text",
                "label" => trans ('HCResources::resources_thumbs.width'),
            ],
            'height'       => [
                "type"  => "text",
                "label" => trans ('HCResources::resources_thumbs.height'),
            ],
            'fit'          => [
                "type"  => "text",
                "label" => trans ('HCResources::resources_thumbs.fit'),
            ],
            'aspect_ratio' => [
                "type"  => "text",
                "label" => trans ('HCResources::resources_thumbs.global'),
            ],

        ];
    }

    /**
     * Create item
     *
     * @param null $data
     * @return mixed
     */
    protected function __create (array $data = null)
    {
        if (is_null ($data))
            $data = $this->getInputData ();

        $record = HCThumbs::create (array_get ($data, 'record'));

        return $this->getSingleRecord ($record->id);
    }

    /**
     * Updates existing item based on ID
     *
     * @param $id
     * @return mixed
     */
    protected function __update (string $id)
    {
        $record = HCThumbs::findOrFail ($id);

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
        HCThumbs::destroy ($list);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed|void
     */
    protected function __forceDelete (array $list)
    {
        HCThumbs::onlyTrashed ()->whereIn ('id', $list)->forceDelete ();
    }

    /**
     * Restore multiple records
     *
     * @param $list
     * @return mixed|void
     */
    protected function __restore (array $list)
    {
        HCThumbs::whereIn ('id', $list)->restore ();
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
            $select = HCThumbs::getFillableFields();

        $list = HCThumbs::with($with)->select($select)
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
     * Creating data list
     * @return mixed
     */
    public function listData()
    {
        return $this->createQuery()->paginate($this->recordsPerPage);
    }

    /**
     * Creating data list based on search
     * @return mixed
     */
    public function search()
    {
        if (!request('q'))
            return [];

        //TODO set limit to start search

        return $this->createQuery()->get();
    }

    /**
     * List search elements
     * @param $list
     * @return mixed
     */
    protected function listSearch (Builder $list)
    {
        if (request ()->has ('q')) {
            $parameter = request ()->input ('q');

            $list = $list->where (function ($query) use ($parameter) {
                $query->where ('name', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('width', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('height', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('fit', 'LIKE', '%' . $parameter . '%')
                    ->orWhere ('aspect_ratio', 'LIKE', '%' . $parameter . '%');
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
        (new HCThumbsValidator())->validateForm ();

        $_data = request ()->all ();

        array_set ($data, 'record.name', array_get ($_data, 'name'));
        array_set ($data, 'record.width', array_get ($_data, 'width'));
        array_set ($data, 'record.height', array_get ($_data, 'height'));
        array_set ($data, 'record.fit', array_get ($_data, 'fit.0'));
        array_set ($data, 'record.global', array_get ($_data, 'global.0'));

        return $data;
    }

    /**
     * Getting single record
     *
     * @param $id
     * @return mixed
     */
    public function getSingleRecord (string $id)
    {
        $with = [];

        $select = HCThumbs::getFillableFields ();

        $record = HCThumbs::with ($with)
            ->select ($select)
            ->where ('id', $id)
            ->firstOrFail ();

        return $record;
    }
}
