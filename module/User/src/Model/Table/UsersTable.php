<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\Filter;
use Laminas\I18n\Filter\Alnum;
use Laminas\I18n\Validator\Alnum as ValidatorAlnum;
use Laminas\Validator;
use Laminas\Validator\Db\RecordExists;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n\Validator\IsInt;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator\Db\NoRecordExists;
use User\Model\Entity\UserEntity;

class UsersTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'users';
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAccountById(int $id)
    {
        $sqlQuery = $this->sql->select()
            ->join('roles', 'roles.id='. $this->table .'.role_id', ['id', 'role'])
            ->where(['users.id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute()->current();

        if (!$handler) {
            return null;
        }

        $classMethod = new ClassMethodsHydrator();
        $entity = new UserEntity();
        $classMethod->hydrate($handler, $entity);

        return $entity;
    }

    public function fetchAccountByEmail(string $email)
    {
        $sqlQuery = $this->sql->select()
            ->join('roles', 'roles.id='. $this->table .'.role_id', ['id', 'role'])
            ->where(['email' => $email]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute()->current();

        if (!$handler) {
            return null;
        }

        $classMethod = new ClassMethodsHydrator();
        $entity = new UserEntity();
        $classMethod->hydrate($handler, $entity);

        return $entity;
    }

    public function fetchAllAccounts($paginated = false)
    {
        $sqlQuery = $this->sql->select()
            ->join('roles', 'roles.id='. $this->table .'.role_id', ['role'])
            ->where(['users.active' => 1])
            ->order('users.created ASC');

        if ($paginated) {
            $classMethod = new ClassMethodsHydrator();
            $entity = new UserEntity();
            $resultSet = new HydratingResultSet($classMethod, $entity);
            
            $paginatorAdapter = new DbSelect($sqlQuery, $this->adapter, $resultSet);
            $paginator = new Paginator($paginatorAdapter);

            return $paginator;
        }

        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        if (!$handler) {
            return null;
        }

        $classMethod = new ClassMethodsHydrator();
        $entity = new UserEntity();
        $resultSet = new HydratingResultSet($classMethod, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }

    public function getLoginFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'recall',
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
                        'name' => Validator\InArray::class,
                        'options' => [
                            'haystack' => [0, 1],
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

    public function getEmailFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'new_email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirm_new_email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                    [
                        'name' => Validator\Identical::class,
                        'options' => [
                            'token' => 'new_email',
                            'messages' => [
                                Validator\Identical::NOT_SAME => 'Email do not match!',
                            ],
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

    public function getPasswordFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'current_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'new_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirm_new_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                        [
                            'name' => Validator\Identical::class,
                            'options' => [
                                'token' => 'new_password',
                                'messages' => [
                                    Validator\Identical::NOT_SAME => 'Passwords do not match!',
                                ],
                            ],
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

    public function getUsernameFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'current_username',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Alnum::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 5,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Username must have at least 5 characters',
                                Validator\StringLength::TOO_LONG => 'Username must have at most 25 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => ValidatorAlnum::class,
                        'options' => [
                            'messages' => [
                                ValidatorAlnum::NOT_ALNUM => 'Username must consist alphanumeric characters only',
                            ],
                        ],
                    ],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'username',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'new_username',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Alnum::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 5,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Username must have at least 5 characters',
                                Validator\StringLength::TOO_LONG => 'Username must have at most 25 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => ValidatorAlnum::class,
                        'options' => [
                            'messages' => [
                                ValidatorAlnum::NOT_ALNUM => 'Username must consist alphanumeric characters only',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'username',
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

    public function getForgotPasswordFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
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

    public function getResetPasswordFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'new_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirm_new_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                        [
                            'name' => Validator\Identical::class,
                            'options' => [
                                'token' => 'new_password',
                                'messages' => [
                                    Validator\Identical::NOT_SAME => 'Passwords do not match!',
                                ],
                            ],
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

    public function getCreateFormFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'username',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Alnum::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 5,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Username must have at least 5 characters',
                                Validator\StringLength::TOO_LONG => 'Username must have at most 25 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => ValidatorAlnum::class,
                        'options' => [
                            'messages' => [
                                ValidatorAlnum::NOT_ALNUM => 'Username must consist alphanumeric characters only',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'username',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'gender',
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
                            'haystack' => ['Female', 'Male', 'Other'],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirm_email',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StringToLower::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => Validator\EmailAddress::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'min' => 10,
                            'max' => 128,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Email Address must have at least 10 characters',
                                Validator\StringLength::TOO_LONG => 'Email Address must have at most 128 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => NoRecordExists::class,
                        'options' => [
                            'table' => $this->table,
                            'field' => 'email',
                            'adapter' => $this->adapter,
                        ],
                    ],
                    [
                        'name' => Validator\Identical::class,
                        'options' => [
                            'token' => 'email',
                            'messages' => [
                                Validator\Identical::NOT_SAME => 'Email Adresses do not match!',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'birthday',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\Date::class,
                        'options' => [
                            'format' => 'Y-m-d',
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'confirm_password',
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
                            'min' => 8,
                            'max' => 25,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
                            ],
                        ],
                    ],
                    [
                        'name' => Validator\Identical::class,
                        'options' => [
                            'token' => 'password',
                            'messages' => [
                                Validator\Identical::NOT_SAME => 'Passwords do not match!',
                            ],
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

    public function saveAccount(array $data)
    {
        $timeNow = date('Y-m-d H:i:s');

        $values = [
            'username' => $data['username'],
            'email' => mb_strtolower($data['email']),
            'password' => (new Bcrypt())->create($data['password']),
            'birthday' => $data['birthday'],
            'gender' => $data['gender'],
            'role_id' => $this->assignRoleId(),
            'created' => $timeNow,
            'modified' => $timeNow,
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateEmail(string $email, int $id)
    {
        $values = [
            'email' => mb_strtolower($email),
            'modified' => date('Y-m-d H:i:s'),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }


    public function updatePassword(string $password, int $id)
    {
        $values = [
            'password' => (new Bcrypt())->create($password),
            'modified' => date('Y-m-d H:i:s'),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateUsername(string $username, int $id)
    {
        $values = [
            'username' => $username,
            'modified' => date('Y-m-d H:i:s'),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function deleteAccount(int $id)
    {
        $sqlQuery = $this->sql->update()->set(['active' => 0])->where(['id' => $id]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    private function assignRoleId()
    {
        return 2; // member
    }
}