<?php namespace interactivesolutions\honeycombresources\validators\resources;

use interactivesolutions\honeycombcore\http\controllers\HCCoreFormValidator;

class HCThumbsValidator extends HCCoreFormValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules ()
    {
        return [
            'name'   => 'required',
            'width'  => 'numeric|required',
            'height' => 'numeric|required',

        ];
    }
}