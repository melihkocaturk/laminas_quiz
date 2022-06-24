<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Application\Model\Table\CategoriesTable;
use Laminas\Form\Form;
use Laminas\Form\Element;

class CreateForm extends Form
{
    public function __construct(CategoriesTable $categoriesTable)
    {
        parent::__construct('create_quiz');

        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'category_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Category',
                'empty_option' => 'Select...',
                'value_options' => $categoriesTable->fetchAllCategories(),
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'timeout',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Quiz Ends In',
                'empty_option' => 'Select...',
                'value_options' => [
                    '1 day' => '1 Day',
                    '3 days' => '3 Days',
                    '7 days' => '7 Days',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'title',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Title',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 100,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'question',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Question',
            ],
            'attributes' => [
                'required' => true,
                'cols' => 30,
                'maxlength' => 300,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'answers[]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Answers',
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 100,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'user_id',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'add_more',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Add Another Answer',
            ],
            'attributes' => [
                'id' => 'add_more',
                'class' => 'btn btn-sm btn-secondary',
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
            'name' => 'create_quiz',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Create Quiz',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}