<?php

namespace interactivesolutions\honeycombresources\models\resources;

use interactivesolutions\honeycombcore\models\HCUuidModel;

class HCThumbs extends HCUuidModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_resources_thumbs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'active', 'width', 'height', 'fit', 'aspect_ratio'];

}
