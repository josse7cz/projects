<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

/**
 * Base presenter for all application presenters.
 */
class BasePresenter extends Nette\Application\UI\Presenter {

    public function startup() {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect(":Public:Sign:in");
        }
    }

}
