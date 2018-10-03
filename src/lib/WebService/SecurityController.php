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

        if ($form->isSubmitted()) {
            $success = Security::doLogin($form->get("username"), $form->get("password"));

            if ($success) {
                Security::markLogin();
                Routing::route("/");
            }
            else {
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

        $form = new Form("register_form");
        $form->add("text", "username", "Username");
        $form->add("email", "email", "E-Mail Address");
        $form->add("password", "password1", "Password");
        $form->add("password", "password2", "Repeat password");

        if ($form->isSubmitted()) {
            $user = new User();
            $user->setUsername($form->get("username"));
            $user->setPassword($form->get("password"));
            $user->setEmail($form->get("email"));

            DB::getInstance()->getEntityManager()->persist($user);
            DB::getInstance()->getEntityManager()->flush();
        }

        $content->assign("form", $form);

        OutputManager::getInstance()->display($content);
    }
}