<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Form;
use Laminas\Form\Element;

class UsernameForm extends Form
{
    public function __construct()
    {
        parent::__construct('update_username');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'current_username',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Current Username',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 25,
                'autocomplete' => false,
                'class' => 'form-control',
                'readonly' => true,
                'pattern' => '^[a-zA-z0-9]+$',
            ],
        ]);

        $this->add([
            'name' => 'new_username',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'New Username',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 25,
                'autocomplete' => false,
                'class' => 'form-control',
                'pattern' => '^[a-zA-z0-9]+$',
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
            'name' => 'change_username',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}

