<?php namespace interactivesolutions\honeycombresources\app\http\controllers\resources;

use Illuminate\Database\Eloquent\Builder;
use interactivesolutions\honeycombcore\http\controllers\HCBaseController;
use interactivesolutions\honeycombresources\app\models\resources\HCThumbs;
use interactivesolutions\honeycombresources\app\validators\resources\HCThumbsValidator;

class HCThumbsController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminIndex ()
    {
        $config = [
            'title'       => trans ('HCResources::resources_thumbs.page_title'),
            'listURL'     => route ('admin.api.resources.thumbs'),
            'newFormUrl'  => route ('admin.api.form-manager', ['resources-thumbs-new']),
            'editFormUrl' => route ('admin.api.form-manager', ['resources-thumbs-edit']),
            //    'imagesUrl'   => route ('resource.get', ['/']),
            'headers'     => $this->getAdminListHeader (),
        ];

//        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_create'))
//            $config['actions'][] = 'new';

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_update')) {
            $config['actions'][] = 'update';
            $config['actions'][] = 'restore';
        }

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_delete'))
            $config['actions'][] = 'delete';

        if (auth ()->user ()->can ('interactivesolutions_honeycomb_resources_resources_thumbs_search'))
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
     * @param array|null $data
     * @return mixed
     */
    protected function __apiStore (array $data = null)
    {
        if (is_null ($data))
            $data = $this->getInputData ();

        $record = HCThumbs::create (array_get ($data, 'record'));

        return $this->apiShow ($record->id);
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
    public function apiShow (string $id)
    {
        $with = [];

        $select = HCThumbs::getFillableFields ();

        $record = HCThumbs::with ($with)
                          ->select ($select)
                          ->where ('id', $id)
                          ->firstOrFail ();

        return $record;
    }

    /**
     * Updates existing item based on ID
     *
     * @param $id
     * @return mixed
     */
    protected function __apiUpdate (string $id)
    {
        $record = HCThumbs::findOrFail ($id);

        $data = $this->getInputData ();

        $record->update (array_get ($data, 'record'));

        return $this->apiShow ($record->id);
    }

    /**
     * Delete records table
     *
     * @param $list
     * @return mixed
     */
    protected function __apiDestroy (array $list)
    {
        HCThumbs::destroy ($list);

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
        HCThumbs::onlyTrashed ()->whereIn ('id', $list)->forceDelete ();

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
        HCThumbs::whereIn ('id', $list)->restore ();

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
            $select = HCThumbs::getFillableFields ();

        $list = HCThumbs::with ($with)->select ($select)
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
     * @param Builder $list
     * @param string $phrase
     * @return mixed
     */
    protected function searchQuery (Builder $list, string $phrase)
    {
        return $list->where (function ($query) use ($phrase) {
            $query->where ('name', 'LIKE', '%' . $phrase . '%')
                  ->orWhere ('width', 'LIKE', '%' . $phrase . '%')
                  ->orWhere ('height', 'LIKE', '%' . $phrase . '%')
                  ->orWhere ('fit', 'LIKE', '%' . $phrase . '%')
                  ->orWhere ('aspect_ratio', 'LIKE', '%' . $phrase . '%');
        });
    }
}
