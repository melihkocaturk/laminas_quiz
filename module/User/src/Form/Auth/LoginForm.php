<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Form;
use Laminas\Form\Element;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('sign_in');

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
            'name' => 'password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 25,
                'autocomplete' => false,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'recall',
            'type' => Element\Checkbox::class,
            'options' => [
                'label' => 'Remember Me',
                'label_attributes' => [
                    'class' => 'custom-control-label',
                ],
                'use_hidden-element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
            'attributes' => [
                'value' => 0,
                'id' => 'recall',
                'class' => 'custom-control-input',
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
            'name' => 'sign_in',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Sign In',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}