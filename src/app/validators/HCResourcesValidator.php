<?php namespace interactivesolutions\honeycombresources\validators;

use interactivesolutions\honeycombcore\http\controllers\HCCoreFormValidator;

class HCResourcesValidator extends HCCoreFormValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules ()
    {
        return [
            'original_name' => 'required',
            'safe_name'     => 'required',
        ];
    }
}