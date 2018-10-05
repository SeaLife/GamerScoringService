<?php

namespace WebService\Admin;

use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Globals\Annotations\WebResponder;
use Globals\DB;
use Globals\MenuHandler;
use Globals\Routing;
use Globals\Security;
use Output\Http\Content;
use Output\Http\Form;
use Output\Http\OutputManager;
use Output\Http\Validator\ChainValidator;
use Output\Http\Validator\HasNoDatabaseEntryValidator;
use Output\Http\Validator\StringLengthValidator;

/**
 * Admin Controller for managing Roles
 */
class RolesController {

    /** @var EntityRepository */
    private $repo;

    public function __construct () {
        $this->repo = DB::getInstance()->getEntityManager()->getRepository(Role::class);

        MenuHandler::getInstance()->add("Roles", "/admin/roles", function () {
            return Security::hasPermission('R');
        });
    }


    /**
     * @WebResponder(path="/admin/roles", method="GET", requiredPermission="R")
     * @param bool $_validate
     */
    public function viewAllRoles ($_validate = FALSE) {
        /** @var $roles Role[] */
        $roles = $this->repo->findAll();

        $roleNames = array('no parent role');

        foreach ($roles as $role) array_push($roleNames, $role->getName());

        $content = new Content("web/admin/roles/overview.html.twig");
        $content->assign("roles", $roles);

        $addForm = $this->getAddForm($roleNames);
        if ($_validate) $addForm->isValid();

        $content->assign('add_form', $addForm);


        OutputManager::getInstance()->display($content);
    }

    /**
     * @WebResponder(path="/admin/role/{id:\d+}", method="GET", requiredPermission="R")
     * @param $vars
     */
    public function viewRole ($vars) {

    }

    /**
     * @WebResponder(path="/admin/role", method="POST", requiredPermission="R")
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionAdd () {
        $form = $this->getAddForm(array());

        $valid = $form->isValid();

        if ($form->isSubmitted() && $valid) {
            $role = new Role();
            $role->setName($form->get('roleName'));

            if ($form->get('parentRole') != 'no parent role') {
                /** @var $pRole Role */
                $pRole = DB::getInstance()->getEntityManager()->getRepository(Role::class)->findOneBy(
                    array('name' => $form->get('parentRole'))
                );

                if ($pRole == NULL) throw new \InvalidArgumentException('Role ' . $form->get('parentRole') . ' does not exist!');

                $role->setParent($pRole);
            }

            DB::getInstance()->getEntityManager()->persist($role);
            DB::getInstance()->getEntityManager()->flush();

            Routing::route('/admin/roles');
        } else {
            $this->viewAllRoles(TRUE);
        }
    }

    /**
     * @WebResponder(path="/admin/role/{id:\d+}", method="POST", requiredPermission="R")
     * @param $vars
     */
    public function actionModify ($vars) {

    }

    /**
     * @WebResponder(path="/admin/role/{id:\d+}", method="DELETE", requiredPermission="R")
     * @param $vars
     */
    public function actionDelete ($vars) {

    }


    private function getAddForm ($roleNames) {

        $roleValidator = new ChainValidator();
        $roleValidator->add(new HasNoDatabaseEntryValidator(Role::class, 'name'));
        $roleValidator->add(new StringLengthValidator(3));

        $addForm = new Form('add_role', '/admin/role');
        $addForm->add('text', 'roleName', 'Role Name', $roleValidator);
        $addForm->add('dropdown', 'parentRole', 'Parent Role', NULL, $roleNames);

        return $addForm;
    }
}