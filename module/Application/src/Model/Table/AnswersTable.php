<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\AnswerEntity;
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

class AnswersTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'answers';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAnswersById(int $quizId)
    {
        $sqlQuery = $this->sql->select()->where(['quiz_id' => $quizId]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        $classMethod = new ClassMethodsHydrator();
        $entity = new AnswerEntity();

        $resultSet = new HydratingResultSet($classMethod, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }

    public function getAnswerFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'id',
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
                            'table' => $this->table,
                            'field' => 'id',
                            'adapter' => $this->adapter,
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

    public function updateAnswerTally(int $id, int $quizId)
    {
        $sqlQuery = $this->sql->update()
            ->set(['tally' => new Expression('tally + 1')])
            ->where(['id' => $id])
            ->where(['quiz_id' => $quizId]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function saveAnswer(string $answer, int $quizId)
    {
        $values = [
            'quiz_id' => $quizId,
            'answer' => $answer,
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}