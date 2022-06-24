<?php

declare(strict_types=1);

namespace Application\Form\Help;

use Laminas\Captcha\ReCaptcha;
use Laminas\Form\Form;
use Laminas\Form\Element;

class ContactForm extends Form
{
    public function __construct()
    {
        parent::__construct('contact');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'name',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Full Name',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 50,
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
                'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9+_.-]+$',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'message',
            'type' => Element\TextArea::class,
            'options' => [
                'label' => 'Message',
            ],
            'attributes' => [
                'required' => true,
                'cols' => 90,
                'maxlength' => 900,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'captcha',
            'type' => Element\Captcha::class,
            'options' => [
                'label' => 'Are you human?',
                'captcha' => new ReCaptcha([
                    'site_key' => '6Lc4k0MgAAAAAKNgGk47FWCUIItwUGBR7TTK-df3',
                    'secret_key' => '6Lc4k0MgAAAAAGpqORSXkUyJKlPFdR7tiQVZyXTC',
                ]),
            ],
            'attributes' => [
                'required' => true,
                'class' => 'custom-control',
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
            'name' => 'contact_us',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}