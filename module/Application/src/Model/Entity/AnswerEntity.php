<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class AnswerEntity 
{
    protected $id;
    protected $quiz_id;
    protected $answer;
    protected $tally;
    // tallies table columns
    protected $user_id;
    protected $created;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function setQuizId($quizId)
    {
        $this->quiz_id = $quizId;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    public function getTally()
    {
        return $this->tally;
    }

    public function setTally($tally)
    {
        $this->tally = $tally;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

}