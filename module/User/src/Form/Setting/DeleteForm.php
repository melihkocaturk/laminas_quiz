<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Form;
use Laminas\Form\Element;

class DeleteForm extends Form
{
    public function __construct()
    {
        parent::__construct('suspend_account');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'delete_account',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Delete Account',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}

