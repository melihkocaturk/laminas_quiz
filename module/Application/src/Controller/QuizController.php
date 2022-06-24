<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Quiz\AnswerForm;
use Application\Form\Quiz\CreateForm;
use Application\Form\Quiz\DeleteForm;
use Application\Model\Table\AnswersTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;

class QuizController extends AbstractActionController
{
    private $answersTable;
    private $quizzesTable;
    private $talliesTable;
    private $createForm;

    public function __construct(
        AnswersTable $answersTable,
        QuizzesTable $quizzesTable,
        TalliesTable $talliesTable,
        CreateForm $createForm
    )
    {
        $this->answersTable = $answersTable;
        $this->quizzesTable = $quizzesTable;
        $this->talliesTable = $talliesTable;
        $this->createForm = $createForm;
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $quizzes = $this->quizzesTable->fetchAllMyQuizzes((int) $this->authPlugin()->getId());

        return new ViewModel(['quizzes' => $quizzes]);
    }

    public function answerAction()
    {
        $id = (int) $this->params()->fromRoute('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->notFoundAction();
        }

        $info = $this->quizzesTable->fetchQuizById($id);

        if (!$info) {
            return $this->notFoundAction();
        }

        if ($info->getTimeout() < date('Y-m-d H:i:s')) {
            $this->quizzesTable->closeQuiz((int) $info->getId());
            $this->redirect()->refresh();
        }

        if ($info->getStatus() == 'Closed') {
            $this->flashMessenger()->addSuccessMessage('This quiz no longer active. You can only view its results.');
            return $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $info->getId()]);
        }

        $answerForm = new AnswerForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $answerForm->setInputFilter($this->answersTable->getAnswerFormFilter());
            $answerForm->setData($formData);
            
            if ($answerForm->isValid()) {
                try {
                    $data = $answerForm->getData();
                    $this->answersTable->updateAnswerTally((int) $data['id'], (int) $info->getId());
                    $this->talliesTable->saveMyAnswer($data, (int) $info->getId());
                    $this->quizzesTable->updateTotal((int) $info->getId());

                    $this->flashMessenger()->addSuccessMessage('You have succesfully saved your answer.');
                    return $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $info->getId()]);
                } catch (RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel([
            'form' => $answerForm,
            'quiz' => $info,
            'record' => $this->talliesTable,
            'insight' => $this->answersTable
        ]);
    }

    public function createAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $createForm = $this->createForm;
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $createForm->setInputFilter($this->quizzesTable->getCreateFormFilter());
            $createForm->setData($formData);

            if ($createForm->isValid()) {
                try {
                    $data = $createForm->getData();
                    $info = $this->quizzesTable->saveQuiz($data);
                    $id = $info->getGeneratedValue();

                    $answers = (array) $this->params()->fromPost('answers');
                    $answers = array_filter(array_map('strip_tags', $answers));
                    $answers = array_filter(array_map('trim', $answers));
                    $answers = array_slice($answers, 0, 5);

                    foreach ($answers as $answer) {
                        if (mb_strlen($answer) > 100) {
                            $answer = mb_substr($answer, 0, 100);
                        }

                        $this->answersTable->saveAnswer($answer, (int) $id);
                    }

                    $this->flashMessenger()->addSuccessMessage('Quiz succesfully created.');
                    return $this->redirect()->toRoute('quiz', ['action' => 'answer', 'id' => $id]);
                } catch (RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $createForm]);
    }

    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $info = $this->quizzesTable->fetchQuizById($id);

        if (!is_numeric($id) || !$info) {
            return $this->notFoundAction();
        }

        $auth = new AuthenticationService();

        if (!$auth->hasIdentity() || $this->authPlugin()->getId() != $info->getUserId()) {
            $this->quizzesTable->updateViews((int) $info->getId());
        }

        return new ViewModel([
            'quiz' => $info,
            'answersTable' => $this->answersTable,
        ]);
    }

    public function deleteAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $id = (int) $this->params()->fromRoute('id');

        if (!is_numeric($id)) {
            return $this->notFoundAction();
        }

        $info = $this->quizzesTable->fetchQuizById($id);

        if (!$info) {
            return $this->notFoundAction();
        }

        $deleteForm = new DeleteForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $deleteForm->setData($formData);

            if($deleteForm->isValid()) {
                try {
                    if ($request->getPost()->get('delete_quiz') == 'Yes') {
                        if ($info->getUserId() == $this->authPlugin()->getId()) {
                            $this->quizzesTable->deleteQuizById((int) $info->getId());
        
                            $this->flashMessenger()->addSuccessMessage('Quiz successfully deleted.');
                            return $this->redirect()->toRoute('quiz', ['action' => 'index']);
                        }
                        
                        $this->flashMessenger()->addWarningMessage('You can only delete quiz you have posted!');
                        return $this->redirect()->toRoute('home');
                    }

                    return $this->redirect()->toRoute('home');
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $deleteForm, 'quiz' => $info]);
    }
}
