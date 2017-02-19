<?php

namespace interactivesolutions\honeycombresources\forms\resources;

class HCThumbsForm
{
    // name of the form
    protected $formID = 'resources-thumbs';

    // is form multi language
    protected $multiLanguage = 0;

    /**
     * Creating form
     *
     * @param bool $edit
     * @return array
     */
    public function createForm ($edit = false)
    {
        $form = [
            'storageURL' => route ('admin.api.resources.thumbs'),
            'buttons'    => [
                [
                    "class" => "col-centered",
                    "label" => trans ('HCCoreUI::core.button.submit'),
                    "type"  => "submit",
                ],
            ],
            'structure'  => [
                [
                    "type"            => "singleLine",
                    "fieldID"         => "name",
                    "label"           => trans ("HCResources::resources_thumbs.name"),
                    "required"        => 1,
                    "requiredVisible" => 1,
                ], [
                    "type"            => "singleLine",
                    "fieldID"         => "width",
                    "label"           => trans ("HCResources::resources_thumbs.width"),
                    "required"        => 1,
                    "requiredVisible" => 1,
                ], [
                    "type"            => "singleLine",
                    "fieldID"         => "height",
                    "label"           => trans ("HCResources::resources_thumbs.height"),
                    "required"        => 1,
                    "requiredVisible" => 1,
                ], [
                    "type"            => "radioList",
                    "fieldID"         => "fit",
                    "label"           => trans ("HCResources::resources_thumbs.fit"),
                    "required"        => 0,
                    "requiredVisible" => 0,
                    "options"         => formManagerYesNo()
                ], [
                    "type"            => "radioList",
                    "fieldID"         => "aspect_ratio",
                    "label"           => trans ("HCResources::resources_thumbs.aspect_ratio"),
                    "required"        => 0,
                    "requiredVisible" => 0,
                    "options"         => formManagerYesNo()
                ],
            ],
        ];

        if ($this->multiLanguage)
            $form['availableLanguages'] = []; //TOTO implement honeycomb-languages package

        if (!$edit)
            return $form;

        //Make changes to edit form if needed
        // $form['structure'][] = [];

        return $form;
    }
}