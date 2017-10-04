<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

class ProdejPresenter extends BasePresenter {

    /** @var Model\ProductManager @inject */
    public $productManager;

    /** @var Model\BasketManager @inject */
    public $basketManager;

    public function actionDefault($bid = null, $newamount = null) {

        if ($bid !== null && $newamount !== null) {

            $newamount = str_replace(",", ".", $newamount);
            $newamount = str_replace(" ", "", $newamount);

            $this->basketManager->changeAmount($this->getUser()->getId(), $bid, $newamount);

            $this->redirect(":Pokladna:Prodej:default");
        }


        $this->template->celkem = 0;

        $basketProducts = $this->basketManager->findAllOfUser($this->getUser()->getId());

        foreach ($basketProducts as $bp) {
            $this->template->celkem += $bp->amount * $bp->price;
        }


        $this->template->zbozi = $basketProducts;
    }

    public function actionHotovo($id) {

        $this->template->celkem = 0;

        $basketProducts = $this->basketManager->findAllOfUser($this->getUser()->getId(), $id);

        $receipt = $this->basketManager->findReceipt($this->getUser()->getId(), $id);

        $this->template->placeno = 0;

        foreach ($basketProducts as $bp) {
            $this->template->celkem += $bp->amount * $bp->price;
        }
        $this->template->receipt = $receipt;

        $this->template->zbozi = $basketProducts;
    }

    public function actionChangeamount($id) {
        $this->template->produkt = $this->basketManager->findId($id);
    }

    public function actionODebrat($id) {
        $this->basketManager->delete($id);
        $this->flashMessage("Zboží bylo odebráno z košíku", "alert-success");
        $this->redirect(":Pokladna:Prodej:default");
    }

    public function actionPridatzbozi() {

        $products = $this->productManager->findAllOfUser($this->getUser()->getId());


        $this->template->zbozi = $products;
    }

    public function actionVybrat($id) {
        $data["user_id"] = $this->getUser()->getId();
        $data["product_id"] = $id;
        $data["amount"] = 1;
        $this->basketManager->add($data);
        $this->flashMessage("Zboží bylo přidáno do košíku", "alert-success");
        $this->redirect(":Pokladna:Prodej:default");
    }

    public function actionPlatba() {
        $this->template->celkem = 0;

        $this->template->celkem = 0;

        $basketProducts = $this->basketManager->findAllOfUser($this->getUser()->getId());

        foreach ($basketProducts as $bp) {
            $this->template->celkem += $bp->amount * $bp->price;
        }


        $this->template->zbozi = $basketProducts;
    }

    /**
     * Změnit množství form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentZmenitMnozstviForm() {
        $form = new Nette\Application\UI\Form;

        $form->addHidden("id");

        $form->addText("amount")
                ->setDefaultValue($this->template->produkt->amount)
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "zmenitMnozstviFormSucceeded");


        return $form;
    }

    public function zmenitMnozstviFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $data["amount"] = $values["amount"];

        $this->basketManager->update($values["id"], $data);

        $this->flashMessage("Množství bylo upraveno!", "alert-success");
        $this->redirect(":Pokladna:Prodej:default");
    }

    /**
     * Změnit množství form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentDokoncitForm() {
        $form = new Nette\Application\UI\Form;

        $form->addText("placeno");

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "placenoFormSucceeded");


        return $form;
    }

    public function placenoFormSucceeded(Nette\Application\UI\Form $form, $values) {

        require_once __DIR__ . '/../../../lib/eet-master/src/Receipt.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Dispatcher.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/SoapClient.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/UUID.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/Format.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/XMLSecurityDSig.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/XMLSecEnc.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/XMLSecurityKey.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/WSASoap.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/WSSESoap.php';
        require_once __DIR__ . '/../../../lib/eet-master/src/Utils/WSSESoapServer.php';


        $dispatcher = new \Ondrejnov\EET\Dispatcher(__DIR__ . '/../../../lib/eet-master/src/Schema/PlaygroundService.wsdl', __DIR__ . '/../../../lib/eet-master/_cert/eet.key', __DIR__ . '/../../../lib/eet-master/_cert/eet.pem');

        $r = new \Ondrejnov\EET\Receipt();
        $r->uuid_zpravy = \Ondrejnov\EET\Utils\UUID::v4();
        $r->dic_popl = 'CZ72080043';
        $r->id_provoz = '181';
        $r->id_pokl = '1';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 1000;

        $fik = $dispatcher->send($r); // FIK code should be returned
        $bkp = $dispatcher->getBkpCode();
        $pkp = $dispatcher->getPkpCode();

        $idUctenky = $this->basketManager->vytvorUctenku($values["placeno"], $fik, $bkp, $pkp,  $this->getUser()->getId());


        $this->redirect(":Pokladna:Prodej:hotovo", array("id" => $idUctenky));
    }

}
