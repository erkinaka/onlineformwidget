<?php

namespace Application\Model;

use DomainException;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;

/**
 * Description of Onlineform
 *
 * @author Erkin
 */
class Onlineform implements InputFilterAwareInterface {

    public $id;
    public $title;
    public $created_date;
    public $status;
    public $hit;
    public $form_html;
    public $form_xml;
    public $hidden_url;
    public $respond_count;
    public $answer_date;
    public $value;
    public $form_element_title;
    public $public;
    public $respondents_id;
    public $source_ip;
        
    private $inputFilter;

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->created_date = !empty($data['created_date']) ? $data['created_date'] : null;
        $this->status = !empty($data['status']) ? $data['status'] : null;
        $this->hit = !empty($data['hit']) ? $data['hit'] : null;
        $this->form_html = !empty($data['form_html']) ? $data['form_html'] : null;
        $this->form_xml = !empty($data['form_xml']) ? $data['form_xml'] : null;
        $this->hidden_url = !empty($data['hidden_url']) ? $data['hidden_url'] : null;
        $this->respond_count = !empty($data['respond_count']) ? $data['respond_count'] : null;
        $this->public = !empty($data['public']) ? $data['public'] : null;
        $this->respondents_id = !empty($data['respondents_id']) ? $data['respondents_id'] : null;
        $this->source_ip = !empty($data['source_ip']) ? $data['source_ip'] : null;
    }

    public function getInputFilter(): InputFilterInterface {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        $inputFilter->add([
            'name' => 'respond_count',
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 150,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'hidden_url',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 150,
                    ],
                ],
            ],
        ]);


         $inputFilter->add([
            'name' => 'form_html',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',                      
                    ],
                ],
            ],
        ]);
           $inputFilter->add([
            'name' => 'form_xml',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',                      
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter): InputFilterAwareInterface {
         throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

}
