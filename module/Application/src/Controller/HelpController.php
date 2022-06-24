<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Help\ContactForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class HelpController extends AbstractActionController
{
    public function contactAction()
    {
        $contactForm = new ContactForm();
        return (new ViewModel(['form' => $contactForm]));
    }

    public function privacyAction()
    {
        return new ViewModel(['username' => $this->authPlugin()->getUsername()]);
    }

    public function termsAction()
    {
        return new ViewModel();
    }
}
