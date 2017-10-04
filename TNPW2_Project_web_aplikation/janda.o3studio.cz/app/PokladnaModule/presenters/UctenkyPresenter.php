<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

class UctenkyPresenter extends BasePresenter {

    /** @var Model\BasketManager @inject */
    public $basketManager;

    public function actionDefault() {

        $receipts = $this->basketManager->findAllReceiptsOfUser($this->getUser()->getId());

        $this->template->receipts = $receipts;
    }

}
