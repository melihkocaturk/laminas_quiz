<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class QuizEntity 
{
    protected $id;
    protected $user_id;
    protected $category_id;
    protected $title;
    protected $question;
    protected $status;
    protected $views;
    protected $total;
    protected $timeout;
    protected $created;
    // categories table columns
    protected $category;
    // users table columns
    protected $username;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    public function getStatus()
    {
        return $this->status == 1 ? 'Active' : 'Closed';
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
    
    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}