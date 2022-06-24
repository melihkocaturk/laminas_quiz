<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class CategoriesTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'categories';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllCategories()
    {
        $sqlQuery = $this->sql->select()->order('category ASC');
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        $categories = [];

        foreach ($handler as $value) {
            $categories[$value['id']] = $value['category'];
        }

        return $categories;
    }
}