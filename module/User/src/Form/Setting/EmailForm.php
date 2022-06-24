<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Form;
use Laminas\Form\Element;

class EmailForm extends Form
{
    public function __construct()
    {
        parent::__construct('update_email');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 128,
                'autocomplete' => false,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'new_email',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'New E-mail',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 128,
                'autocomplete' => false,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'confirm_new_email',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'Verify New E-mail',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 128,
                'autocomplete' => false,
                'class' => 'form-control',
            ],
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
            'name' => 'change_email',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}

