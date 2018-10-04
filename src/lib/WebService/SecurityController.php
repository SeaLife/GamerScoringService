<?php

namespace WebService;

use App\Entity\User;
use Globals\DB;
use Globals\Routing;
use Globals\Security;
use Globals\Annotations\WebResponder;
use Output\Http\Content;
use Output\Http\Form;
use Output\Http\OutputManager;
use Output\Http\Validator\ChainValidator;
use Output\Http\Validator\EmailValidator;
use Output\Http\Validator\EqualsValidator;
use Output\Http\Validator\HasNoDatabaseEntryValidator;
use Output\Http\Validator\PasswordValidator;
use Output\Http\Validator\StringLengthValidator;

/**
 * Security Web Handler
 */
class SecurityController {

    /**
     * @WebResponder(method={"POST", "GET"}, path="/login", name="View-Layer for the Login-Component")
     */
    public function login () {
        $content = new Content("web/login.html.twig");
        $content->assign("state", Security::isLoggedIn());

        $form = new Form("login_form");
        $form->add("text", "username", "Username");
        $form->add("password", "password", "Password");

        #$form->enableCaptcha();

        if ($form->isSubmitted()) {
            $success = Security::doLogin($form->get("username"), $form->get("password"));

            if ($success) {
                Security::markLogin($form->get("username"));
                Routing::route("/");
            } else {
                $content->assign("error", TRUE);
            }
        }

        $content->assign("login_form", $form);

        OutputManager::getInstance()->display($content);
    }

    /**
     * @WebResponder(method="GET", path="/logout")
     */
    public function logout () {
        session_destroy();
        Routing::route("/");
    }

    /**
     * @WebResponder(method={"POST", "GET"}, path="/register")
     */
    public function register () {
        $content = new Content("web/register.html.twig");

        $passwordValidator = new PasswordValidator(envvar("PASSWORD_SECURITY", 3));
        $passwordValidator->add(new EqualsValidator($_POST["password1"], "Passwords do not match"));
        $passwordValidator->add(new EqualsValidator($_POST["password2"], "Passwords do not match"));

        $userValidator = new ChainValidator(
            new StringLengthValidator(3),
            new HasNoDatabaseEntryValidator(User::class, "username", ":column: is already taken by another user.")
        );

        $emailValidator = new ChainValidator(
            new EmailValidator(),
            new HasNoDatabaseEntryValidator(User::class, "email", ":column: is already taken by another user.")
        );

        $form = new Form("register_form");
        $form->add("text", "username", "Username", $userValidator);
        $form->add("email", "email", "E-Mail Address", $emailValidator);
        $form->add("password", "password1", "Password", $passwordValidator);
        $form->add("password", "password2", "Repeat password", $passwordValidator);

        #$form->enableCaptcha();

        $isValid = $form->isValid();

        if ($form->isSubmitted() && $isValid) {
            $user = new User();
            $user->setUsername($form->get("username"));
            $user->setPassword($form->get("password1"));
            $user->setEmail($form->get("email"));

            DB::getInstance()->getEntityManager()->persist($user);
            DB::getInstance()->getEntityManager()->flush();
            $content->assign("success", TRUE);
        }

        $content->assign("form", $form);

        OutputManager::getInstance()->display($content);
    }
}