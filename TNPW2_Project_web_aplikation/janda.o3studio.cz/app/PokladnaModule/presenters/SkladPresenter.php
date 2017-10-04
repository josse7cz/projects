<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

class SkladPresenter extends BasePresenter {

    /** @var Model\ProductManager @inject */
    public $productManager;

    public function actionDefault() {

        $products = $this->productManager->findAllOfUser($this->getUser()->getId());

        $this->template->zbozi = $products;
    }

    public function actionUpravit($id) {
        $product = $this->productManager->findId($id);

        $this->template->produkt = $product;
    }

    /**
     * Pridat zbozi form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentPridatZboziForm() {
        $form = new Nette\Application\UI\Form;

        $form->addText("name")
                ->setRequired();

        $form->addInteger("price")
                ->setRequired();

        $form->addInteger("amount")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "pridatZboziFormSucceeded");


        return $form;
    }

    public function actionSmazat($id) {
        $data["exist"] = 0;
        $this->productManager->update($id, $data);
        $this->flashMessage("Zboží bylo smazáno!", "alert-warning");
        $this->redirect(":Pokladna:Sklad:default");
        exit;
    }

    public function pridatZboziFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $data["name"] = $values["name"];
        $data["price"] = $values["price"];
        $data["amount"] = $values["amount"];
        $data["user_id"] = $this->getUser()->getId();

        $this->productManager->add($data);

        $this->flashMessage("Zboží bylo přidáno!", "alert-success");
        $this->redirect(":Pokladna:Sklad:default");
    }

    /**
     * Import zbozi form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentImportZboziForm() {
        $form = new Nette\Application\UI\Form;

        $form->addUpload("importfile")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "importZboziFormSucceeded");


        return $form;
    }

    public function importZboziFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $file = $form->values["importfile"];



        $xml = simplexml_load_file($file->temporaryFile) or die("Error: Cannot create object");
        if ($xml === false) {
            $this->flashMessage("Chyba při parsování XML!", "alert-error");
            $this->redirect(":Pokladna:Sklad:default");
            exit;
        }

        $produkty = $xml->xpath("/sklad/produkt");
        $counter = 0;
        foreach ($produkty as $produkt) {
            unset($data);
            $data["name"] = $produkt->nazev;
            $data["price"] = $produkt->cena;
            $data["amount"] = $produkt->amount;
            $data["user_id"] = $this->getUser()->getId();
            $this->productManager->add($data);
            $counter++;
        }





        $this->flashMessage("Zboží bylo importováno celkem " . $counter . " položek zboží!", "alert-success");
        $this->redirect(":Pokladna:Sklad:default");
    }

    /**
     * Pridat zbozi form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentUpravitZboziForm() {
        $form = new Nette\Application\UI\Form;

        $form->addHidden('id');

        $form->addText("name")
                ->setRequired();

        $form->addInteger("price")
                ->setRequired();

        $form->addInteger("amount")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "upravitZboziFormSucceeded");


        return $form;
    }

    public function upravitZboziFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $data["name"] = $values["name"];
        $data["price"] = $values["price"];
        $data["amount"] = $values["amount"];

        $this->productManager->update($values["id"], $data);

        $this->flashMessage("Zboží bylo upraveno!", "alert-success");
        $this->redirect(":Pokladna:Sklad:default");
    }

    public function actionExport() {
        $products = $this->productManager->findAll();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= "<sklad>";

        foreach ($products as $produkt) {
            $xml .= "<produkt>";
            $xml .= "<nazev>" . $produkt->name . "</nazev>";
            $xml .= "<cena>" . $produkt->price . "</cena>";
            $xml .= "<mnozstvi>" . $produkt->amount . "</mnozstvi>";
            $xml .= "</produkt>";
        }
        $xml .= "</sklad>";
        \header('Content-Encoding: UTF-8');
        \header('Content-Type: text/csv; charset=utf-8');
        \header('Content-Disposition: attachment; filename=export.xml');
        $output = \fopen('php://output', 'w');
        \fputs($output, $xml);

        exit();
    }

    public function actionImport() {
        
    }

}
