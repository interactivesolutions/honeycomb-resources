<?php

use interactivesolutions\honeycombresources\app\models\HCResources;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManagerStatic as Image;

if (!function_exists ('generateResourceCacheLocation')) {

    /**
     * Generating resource cache location and name
     *
     * @param $id
     * @param int|null $width
     * @param int|null $height
     * @param null $fit
     * @return mixed
     */
    function generateResourceCacheLocation ($id, $width = 0, $height = 0, $fit = null)
    {
        $path = storage_path ('app/') . 'cache/' . str_replace ('-', '/', $id) . '/';

        if (!is_dir ($path))
            mkdir ($path, 0755, true);

        $path .= $width . '_' . $height;

        if ($fit)
            $path .= '_fit';

        return $path;
    }

    /**
     * Generating resource public location and name
     *
     * @param $id
     * @param int|null $width
     * @param int|null $height
     * @param null $fit
     * @return mixed
     */
    function generateResourcePublicLocation ($id, $width = 0, $height = 0, $fit = null)
    {
        $path = public_path ('thumbs/') . str_replace ('-', '/', $id) . '/';

        if (!is_dir ($path))
            mkdir ($path, 0755, true);

        $path .= $width . '_' . $height;

        if ($fit)
            $path .= '_fit';

        return $path;
    }
}

if (!function_exists ('createImage')) {
    /**
     * Creating image based on provided data
     *
     * @param $source
     * @param $destination
     * @param int $width
     * @param int $height
     * @param bool $fit
     * @return bool
     */
    function createImage ($source, $destination, $width = 0, $height = 0, $fit = false)
    {
        if ($width == 0)
            $width = null;

        if ($height == 0)
            $height = null;

        $image = Image::make ($source);

        if ($fit)
            $image->fit ($width, $height, function (Constraint $constraint) {
                $constraint->upsize ();
            });
        else
            $image->resize ($width, $height, function (Constraint $constraint) {
                $constraint->upsize ();
                $constraint->aspectRatio ();
            });

        $image->save ($destination);

        return true;
    }
}

if (!function_exists('getResourceOriginalName'))
{
    /**
     * Getting resource name
     *
     * @param string $id
     * @return string - original name of the resource
     */
    function getResourceOriginalName(string $id)
    {
        return HCResources::findOrFail($id)->original_name;
    }
}