<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class TalliesTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'tallies';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function hasAnswered(int $quizId, int $userId)
    {
        $sqlQuery = $this->sql->select()->where(['quiz_id' => $quizId])->where(['user_id' => $userId]);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute()->current();
    }

    public function saveMyAnswer(array $data, int $quizId)
    {
        $values = [
            'quiz_id' => $quizId,
            'answer_id' => $data['id'],
            'user_id' => $data['user_id'],
            'created' => date('Y-m-d H:i:s'),
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt =  $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}