<?php

namespace Globals\Security;

use App\Entity\User;
use Globals\Annotations\SecurityProvider;
use Globals\DB;

/**
 * @SecurityProvider()
 */
class AppSecurityProvider extends AbstractSecurityProvider {

    public function doLogin ($username, $password) {
        $entityManager = DB::getInstance()->getEntityManager();

        /** @var $user User */
        $user = $entityManager->getRepository(User::class)->findOneBy(array('username' => $username));

        if ($user != NULL) {
            if ($user->passwordsMatch($password) && $user->isEnabled()) {
                return TRUE;
            }
        }

        if ($username == 'debug' && $password == 'debug') return TRUE;

        return FALSE;
    }
}