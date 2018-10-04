<?php

namespace Console;

use App\Entity\Role;
use Globals\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConfigureRoleCommand extends Command {

    protected function configure () {
        $this->setName('security:modify-role');
        $this->setDescription("Creates a new Role");
    }

    protected function execute (InputInterface $input, OutputInterface $output) {
        set_time_limit(0);

        $roleName = $this->askFor($input, $output, 'Role to configure: ');

        /** @var $role Role */
        $role = DB::getInstance()->getEntityManager()->getRepository(Role::class)->findOneBy(
            array('name' => $roleName)
        );

        if ($role) {
            $output->writeln("Found Role (" . $role->getId() . ")");

            $action = $this->askFor($input, $output, 'Action (allow, deny, list): ');

            if ($action == 'allow') {

                $flag = $this->askFor($input, $output, 'Flag to be added: ');
                if (!$role->isAllowed($flag)) {
                    $role->allow($flag);
                    $output->writeln("Success");
                } else {
                    $output->writeln("Success (flag was set before)");
                }

            } else if ($action == 'deny') {

                $flag = $this->askFor($input, $output, 'Flag to be denied: ');
                if ($role->isAllowed($flag)) {
                    $role->deny($flag);
                    $output->writeln("Success");
                } else {
                    $output->writeln("Success (flag was not set before)");
                }
            } else if ($action == 'list') {
                $flags = join("\n", $role->getFlags());
                $output->writeln("Flags configured for the requested role: ");
                $output->writeln($flags);

                $this->showParentFlags($output, $role);
            }

            DB::getInstance()->getEntityManager()->persist($role);
            DB::getInstance()->getEntityManager()->flush();

        } else {
            $output->writeln("[ERROR] Role not found.");
        }
    }

    private function showParentFlags (OutputInterface $output, Role $role) {
        if ($role->getParent() != NULL) {
            $parentRole = $role->getParent();
            $flags      = join("\n", $parentRole->getFlags());

            $output->writeln("Flags found in the parent role (" . $parentRole->getName() . ")");
            $output->writeln($flags);

            $this->showParentFlags($output, $parentRole);
        }
    }

    private function askFor ($input, $output, $message) {
        $helper = $this->getHelper('question');

        $question = new Question($message, NULL);

        return $helper->ask($input, $output, $question);
    }
}