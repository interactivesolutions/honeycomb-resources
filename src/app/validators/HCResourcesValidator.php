<?php

namespace interactivesolutions\honeycombresources\app\validators;


use InteractiveSolutions\HoneycombCore\Http\Controllers\HCCoreFormValidator;

class HCResourcesValidator extends HCCoreFormValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'original_name' => 'required',
            'safe_name' => 'required',
        ];
    }
}