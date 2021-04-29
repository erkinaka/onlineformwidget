<?php

namespace Application\Form;

use Laminas\Form\Form;

class FormEkleForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('forms');
         $this->setAttribute('method', 'POST');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setAttribute('id', 'addform');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
                'label' => '',
            ],
        ]);

        $this->add([
            'name' => 'form_html',
            'type' => 'text',
            'options' => [
                'label' => '',
            ],
        ]);
        $this->add([
            'name' => 'form_xml',
            'type' => 'text',
            'options' => [
                'label' => '',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Add',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}