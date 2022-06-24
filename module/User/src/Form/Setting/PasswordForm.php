<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Form;
use Laminas\Form\Element;

class PasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('update_password');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'current_password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Current Password',
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
            'name' => 'new_password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'New Password',
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
            'name' => 'confirm_new_password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Verify New Password',
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
            'name' => 'change_password',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}