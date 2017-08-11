<?php

namespace interactivesolutions\honeycombresources\app\models;

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
    protected $fillable = ['id', 'original_name', 'size', 'path', 'mime_type', 'extension', 'checksum'];

    /**
     * Get file path of the resource
     *
     * @return string
     */
    public function file_path()
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . $this->path);
    }

}
