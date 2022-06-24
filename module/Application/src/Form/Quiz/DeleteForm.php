<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Laminas\Form\Form;
use Laminas\Form\Element;

class DeleteForm extends Form
{
    public function __construct()
    {
        parent::__construct('remove_quiz');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'user_id',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'delete_quiz',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Delete Quiz',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}

