<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\QuizEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Validator;
use Laminas\Validator\Db\RecordExists;

class QuizzesTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'quizzes';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchQuizById(int $id)
    {
        $sqlQuery = $this->sql->select()
            ->join('categories', 'categories.id='. $this->table .'.category_id', ['category'])
            ->join('users', 'users.id='. $this->table .'.user_id', ['user_id' => 'id', 'username'])
            ->where([$this->table.'.status' => 1])
            ->where(['quizzes.id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute()->current();

        // dump($handler); die();

        if (!$handler) {
            return null;
        }

        $classMethod = new ClassMethodsHydrator();
        $entity = new QuizEntity();
        $classMethod->hydrate($handler, $entity);

        return $entity;
    }

    public function fecthLatestQuizzes()
    {
        $sqlQuery = $this->sql->select()
            ->join('categories', 'categories.id='. $this->table .'.category_id', ['category'])
            ->order('created DESC');
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        $classMethod = new ClassMethodsHydrator();
        $entity = new QuizEntity();

        $resultSet = new HydratingResultSet($classMethod, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }

    public function fetchAllMyQuizzes(int $userId)
    {
        $sqlQuery = $this->sql->select()
            ->join('categories', 'categories.id='. $this->table .'.category_id', ['category'])
            ->join('users', 'users.id='. $this->table .'.user_id', ['username'])
            ->where([$this->table.'.user_id' => $userId])
            ->where([$this->table.'.status' => 1])
            ->order('created ASC');
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        $classMethod = new ClassMethodsHydrator();
        $entity = new QuizEntity();

        $resultSet = new HydratingResultSet($classMethod, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }

    public function getCreateFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'title',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 100,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Title must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Title must have at most 100 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'category_id',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\ToInt::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => 'categories',
                            'field' => 'id',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'timeout',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\InArray::class,
                        'options' => [
                            'haystack' => ['1 day', '3 days', '7 days'],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'question',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 300,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Question must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Question must have at most 300 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'user_id',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\ToInt::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => 'users',
                            'field' => 'id',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'csrf',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\Csrf::class,
                        'options' => [
                            'messages' => [
                                Validator\Csrf::NOT_SAME => 'Oops! Refill the form',
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    public function saveQuiz(array $data)
    {
        $timeNow = date('Y-m-d H:i:s');

        $values = [
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'question' => $data['question'],
            'timeout' => date('Y-m-d H:i:s', strtotime("+".$data['timeout'])),
            'created' => $timeNow,
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function closeQuiz(int $id)
    {
        $sqlQuery = $this->sql->update()
            ->set(['status' => 0])
            ->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function deleteQuizById(int $id)
    {
        $sqlQuery = $this->sql->update()
            ->set(['status' => 0])
            ->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateViews(int $id)
    {
        $sqlQuery = $this->sql->update()
            ->set(['views' => new Expression('views + 1')])
            ->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateTotal(int $id)
    {
        $sqlQuery = $this->sql->update()
            ->set(['total' => new Expression('total + 1')])
            ->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}