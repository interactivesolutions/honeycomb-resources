<?php

namespace interactivesolutions\honeycombresources\models;

use interactivesolutions\honeycombcore\models\HCUuidModel;

class HCResources extends HCUuidModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'original_name', 'safe_name', 'size', 'path'];

}
