<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\QuizzesTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $quizzesTable;

    public function __construct(QuizzesTable $quizzesTable)
    {
        $this->quizzesTable = $quizzesTable;
    }

    public function indexAction()
    {
        $quizzes = $this->quizzesTable->fecthLatestQuizzes();

        return new ViewModel(['quizzes' => $quizzes]);
    }
}
