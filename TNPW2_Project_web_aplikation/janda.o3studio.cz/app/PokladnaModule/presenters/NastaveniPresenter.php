<?php

namespace App\PokladnaModule\Presenters;

use Nette;
use App\Model;

class NastaveniPresenter extends BasePresenter {

    /** @var Model\UserManager @inject */
    public $userManager;

    public function actionDefault() {

        $this->template->userItem = $this->userManager->findById($this->getUser()->getId());
    }

    /**
     * Pridat zbozi form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentChangeSettingsForm() {
        $form = new Nette\Application\UI\Form;

        $form->addEmail("email")
                ->setDisabled(true)
                ->setDefaultValue($this->template->userItem->email);

        $form->addText("ico")
                ->setDefaultValue($this->template->userItem->settings_ico);

        $form->addText("dic_popl")
                ->setDefaultValue($this->template->userItem->dic_popl);

        $form->addText("id_provoz")
                ->setDefaultValue($this->template->userItem->id_provoz);

        $form->addText("id_pokl")
                ->setDefaultValue($this->template->userItem->id_pokl);


        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "changeSettingsFormSucceeded");

        return $form;
    }

    public function changeSettingsFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $data["settings_ico"] = $values["ico"];
        $data["dic_popl"] = $values["dic_popl"];
        $data["id_provoz"] = $values["id_provoz"];
        $data["id_pokl"] = $values["id_pokl"];

        $this->userManager->update($this->getUser()->getId(), $data);

        $this->flashMessage("Nastavení bylo změněno!", "alert-success");
        $this->redirect(":Pokladna:Nastaveni:default");
    }

    protected function createComponentChangePasswordForm() {
        $form = new Nette\Application\UI\Form;

        $form->addPassword("password")
                ->setRequired(true);

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "changePasswordFormSucceeded");

        return $form;
    }

    public function changePasswordFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $this->userManager->changePassword($this->getUser()->getId(), $values["password"]);


        $this->flashMessage("Heslo bylo změněno!", "alert-success");
        $this->redirect(":Pokladna:Nastaveni:default");
    }

}
