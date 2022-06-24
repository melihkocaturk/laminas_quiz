<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class ForgotPasswordTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'forgot_password';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function clearOldTokens()
    {
        $sqlQuery = $this->sql->delete()->where(['created < ?' => Date('Y-m-d H:i:s', time() - (3600 * 72))]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function deleteToken(int $userId)
    {
        $sqlQuery = $this->sql->delete()->where(['user_id' => $userId]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function fetchToken(string $token, int $userId)
    {
        $sqlQuery = $this->sql->select()->where(['token' => $token])->where(['user_id' => $userId]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute()->current();
    }

    public function generateToken(int $length)
    {
        if ($length < 8 && $length > 40) {
            throw new \LengthException('Token length mus be in range 8-40.');
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $token = '';

        for ($i=0; $i < $length; $i++) { 
            $random = rand(0, strlen($chars) - 1);
            $token .= substr($chars, $random, 1);
        }

        return $token;
    }

    public function saveToken(string $token, int $userId)
    {
        $values = [
            'user_id' => $userId,
            'token' => $token,
            'created' => date('Y-m-d H:i:s'),
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}