<?php

namespace App\PublicModule\Presenters;

use Nette;
use App\Forms;

class SignPresenter extends BasePresenter {

    /** @var \App\Model\UserManager @inject */
    public $userManager;

    public function actionUp($id = null) {

        require_once __DIR__ . '/../../../lib/Facebook/autoload.php';
        $fb = new \Facebook\Facebook([
            'app_id' => '1902384453382431', // Replace {app-id} with your app id
            'app_secret' => 'e6d3391d6e47d46188517f96b6167852',
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('http://janda.o3studio.cz/sign/fb', $permissions);
        $this->template->fbLoginUrl = $loginUrl;
    }

    public function actionFb() {

        require_once __DIR__ . '/../../../lib/Facebook/autoload.php';

        $out = "";

        $fb = new \Facebook\Facebook([
            'app_id' => '1902384453382431', // Replace {app-id} with your app id
            'app_secret' => 'e6d3391d6e47d46188517f96b6167852',
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                print('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                print('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

// Logged in
        $out .= '<h3>Access Token</h3>';
        //   $out .= var_dump($accessToken->getValue());
// The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $out .= '<h3>Metadata</h3>';
        //  $out .= var_dump($tokenMetadata);
// Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId('1902384453382431'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }

            $out .= '<h3>Long-lived</h3>';
            //$out .= var_dump($accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

        $fb->setDefaultAccessToken((string) $accessToken);
        $response = $fb->get('/me?locale=en_US&fields=name,email');
        $userNode = $response->getGraphUser();


        $email = $userNode->getField('email');


        $fbUser = $this->userManager->findByFbId($tokenMetadata->getUserId());
        //neexistuje
        if ($fbUser === false) {
            $this->userManager->addFbuser($tokenMetadata->getUserId(), $email);
        }
        //Tak znovu, už musí existovat
        $fbUser = $this->userManager->findByFbId($tokenMetadata->getUserId());


        $this->getUser()->login($this->userManager->loginOverride($fbUser->id));
        $this->redirect(":Pokladna:Prodej:default");
    }

    public function actionIn() {
        $this->setLayout(FALSE);

        require_once __DIR__ . '/../../../lib/Facebook/autoload.php';
        $fb = new \Facebook\Facebook([
            'app_id' => '1902384453382431', // Replace {app-id} with your app id
            'app_secret' => 'e6d3391d6e47d46188517f96b6167852',
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('http://janda.o3studio.cz/sign/fb', $permissions);
        $this->template->fbLoginUrl = $loginUrl;
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new Nette\Application\UI\Form;

        $form->addEmail("email")
                ->setRequired();

        $form->addPassword("password")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "signInFormSucceeded");


        return $form;
    }

    public function signInFormSucceeded(Nette\Application\UI\Form $form, $values) {

        //$this->userManager->add($values["email"], $values["email"], $values["password"]);

        try {
            $this->getUser()->login($values["email"], $values["password"]);
            unset($data);
            $now = new \DateTime();
            $data["last_login"] = $now->format("Y-m-d H:i:s");
            $this->userManager->update($this->getUser()->getId(), $data);
            $this->redirect(":Pokladna:Prodej:default");
        } catch (Nette\Security\AuthenticationException $exc) {
            $this->flashMessage("Nepodařilo se Vás přihlásit.", "error");
            $this->redirect("Sign:in");
        }
    }

    public function actionOut() {
        $this->getUser()->logout();
    }

    /**
     * Sign-up form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignUpForm() {
        $form = new Nette\Application\UI\Form;

        $form->addEmail("email")
                ->setRequired();

        $form->addCheckbox("souhlas")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "signUpFormSucceeded");


        return $form;
    }

    public function signUpFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $randomPassword = \Nette\Utils\Random::generate(10, "a-zA-Z0-9");

        $existing = $this->userManager->findByEmail($values["email"]);
        if ($existing === false) {

            \mail($values["email"], "Nová registrace", "Registraci potvrdíme přihlášením. Heslo: " . $randomPassword);

            $this->userManager->add($values["email"], $values["email"], $randomPassword);

            $this->redirect("Sign:uped");
        }
        else
        {
            $this->flashMessage("Registrace již existuje. Přihlašte se!");
            $this->redirect("Sign:in");
        }
    }

    public function actionRecovery() {
        
    }

    /**
     * Sign-up form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentRecoveryForm() {
        $form = new Nette\Application\UI\Form;

        $form->addEmail("email")
                ->setRequired();

        $form->addSubmit("submit");

        $form->onSuccess[] = array($this, "recoveryFormSucceeded");


        return $form;
    }

    public function recoveryFormSucceeded(Nette\Application\UI\Form $form, $values) {

        $randomHash = \Nette\Utils\Random::generate(30, "a-zA-Z0-9");

        $userX = $this->userManager->findByEmail($values["email"]);

        if ($userX === false) {
            $this->flashMessage("Tento uživatel neexistuje!");
            $this->redirect("Sign:recovery");
        } else {

            \mail($values["email"], "Obnovení hesla", "Pro vygenerování nového hesla klikněte na následující odkaz: http://janda.o3studio.cz/sign/hash/" . $randomHash);

            unset($data);
            $data["hash"] = $randomHash;
            $this->userManager->update($userX->id, $data);

            $this->flashMessage("Na Vaši emailovou adresu byl zaslán odkaz pro vygenerování nového hesla!");
            $this->redirect("Sign:recovered");
        }
    }

    public function actionHash($id) {
        $itemX = $this->userManager->findByHash($id);
        if ($itemX === null) {
            $this->flashMessage("Obnovovací kód nenalezen!");
            $this->redirect("Sign:recovery");
        } else {
            $randomPassword = \Nette\Utils\Random::generate(10, "a-zA-Z0-9");

            \mail($itemX->email, "Nové heslo", "Bylo Vám vygenerováno nové heslo: " . $randomPassword);


            $this->userManager->changePassword($itemX->id, $randomPassword);
            unset($data);
            $data["hash"] = null;
            $this->userManager->update($itemX->id, $data);

            $this->redirect("Sign:done");
        }
    }

}
