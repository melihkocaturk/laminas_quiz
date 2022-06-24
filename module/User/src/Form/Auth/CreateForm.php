<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Form;
use Laminas\Form\Element;

class CreateForm extends Form
{
    public function __construct()
    {
        parent::__construct('new_account');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'username',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 25,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'gender',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Gender',
                'empty_option' => 'Select...',
                'value_options' => [
                    'Female' => 'Female',
                    'Male' => 'Male',
                    'Other' => 'Other',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

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
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'confirm_email',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'Verify E-mail',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 128,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'birthday',
            'type' => Element\DateSelect::class,
            'options' => [
                'label' => 'Birthday',
                'create_empty_option' => true,
                'max_year' => date('Y') - 18,
                'year_attributes' => [
                    'class' => 'custom-select w-30',
                ],
                'month_attributes' => [
                    'class' => 'custom-select w-30',
                ],
                'day_attributes' => [
                    'class' => 'custom-select w-30',
                ],
            ],
            'attributes' => [
                'required' => true,
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
            'name' => 'confirm_password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Verify Password',
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
            'name' => 'csrf',
            'type' => Element\Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 5 minutes
                ],
            ],
        ]);

        $this->add([
            'name' => 'create_account',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Create Account',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}