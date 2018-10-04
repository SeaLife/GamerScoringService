<?php

namespace Console;

use App\Entity\Role;
use Globals\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddRoleCommand extends Command {

    protected function configure () {
        $this->setName('security:create-role');
        $this->setDescription("Creates a new Role");
    }

    protected function execute (InputInterface $input, OutputInterface $output) {
        set_time_limit(0);

        $roleName = $this->askFor($input, $output, 'Enter the name of the new Role: ');
        $parent   = $this->askFor($input, $output, 'Enter the name of the parent Role, leave empty if there is no role: ');

        $role = new Role();
        $role->setName($roleName);

        if (!empty($parent)) {
            $parent = DB::getInstance()->getEntityManager()->getRepository(Role::class)->findOneBy(
                array('name' => $parent)
            );

            if (empty($parent)) {
                throw new \InvalidArgumentException("Parent does not exist.");
            }

            /** @noinspection PhpParamsInspection */
            $role->setParent($parent);
        }

        DB::getInstance()->getEntityManager()->persist($role);
        DB::getInstance()->getEntityManager()->flush();
    }

    private function askFor ($input, $output, $message) {
        $helper = $this->getHelper('question');

        $question = new Question($message, NULL);

        return $helper->ask($input, $output, $question);
    }
}