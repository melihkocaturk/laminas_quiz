<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Laminas\Form\Form;
use Laminas\Form\Element;

class AnswerForm extends Form
{
    public function __construct()
    {
        parent::__construct('choose_answer');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => Element\Radio::class,
            'options' => [
                'label_attributes' => [
                    'class' => 'form-check-label',
                ],
                'disable_inarray_validator' => true,
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-check-input',
            ],
        ]);

        $this->add([
            'name' => 'user_id',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'csrf',
            'type' => Element\Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 5 minutes
                ],
            ],
        ]);

        $this->add([
            'name' => 'select_answer',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save Answer',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}