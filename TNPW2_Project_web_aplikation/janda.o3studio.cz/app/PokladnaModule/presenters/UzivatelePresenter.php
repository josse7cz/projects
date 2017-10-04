<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

class UzivatelePresenter extends BasePresenter {

    /** @var Model\UserManager @inject */
    public $userManager;

    public function actionDefault() {
        if (!$this->getUser()->isInRole('admin')) {
            echo "Nemáš právo!";
            exit;
        }

        $this->template->users = $this->userManager->findAll();
    }
    
     public function actionSmazat($id) {
        if (!$this->getUser()->isInRole('admin')) {
            echo "Nemáš právo!";
            exit;
        }

        $this->userManager->delete($id);
        $this->flashMessage("Uživatel byl smazán", "alert-info");
        $this->redirect(":Pokladna:Uzivatele:default");
        
    }

}
