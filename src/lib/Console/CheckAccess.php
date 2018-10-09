<?php

namespace Console;

use App\Entity\Role;
use Globals\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @codeCoverageIgnore
 */
class CheckAccess extends Command {

    protected function configure () {
        $this->setName('security:check-role');
        $this->setDescription("Creates a new Role");
    }

    protected function execute (InputInterface $input, OutputInterface $output) {
        set_time_limit(0);

        $roleName = $this->askFor($input, $output, 'Role to check: ');

        /** @var $role Role */
        $role = DB::getInstance()->getEntityManager()->getRepository(Role::class)->findOneBy(
            array('name' => $roleName)
        );

        if ($role) {
            $output->writeln("Found Role (" . $role->getId() . ")");

            $action = $this->askFor($input, $output, 'Permission Flag: ');


            if ($role->isAllowed($action)) {
                if ($role->isAllowed($action, FALSE)) {
                    $output->writeln("Is allowed directly.");
                } else {
                    $output->writeln("Is allowed through a other role.");
                }
            } else {
                $output->writeln("Is not allowed.");
            }

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