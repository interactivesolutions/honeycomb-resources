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
    {
        $form = [
            'buttons'    => [
                [
                    "class" => "col-centered",
                    "type"  => "submit",
                ],
            ],
            'structure'  => [
                [
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
