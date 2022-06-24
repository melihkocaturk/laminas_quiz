<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Captcha\Image;

class ForgotPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('forgot_password');

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
            'name' => 'captcha',
            'type' => Element\Captcha::class,
            'options' => [
                'label' => 'Are you human?',
                'captcha' => new Image([
                    'font' => DOOR . DS . 'fonts/ubuntu.ttf',
                    'fsize' => 55,
                    'wordLen' => 6,
                    'imgAlt' => 'captcha image',
                    'height' => 100,
                    'width' => 300,
                    'dotNoiseLevel' => 220,
                    'lineNoiseLevel' => 18,
                ]),
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 6,
                'class' => 'custom-control',
                'captch' => (new Element\Captcha())->getInputSpecification(), // Validation
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
            'name' => 'forgot_password',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Send Password',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}