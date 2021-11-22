<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public function __construct(string $name = null, UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $this->userManager->sayHello($input->getArgument('name'));

        $output->writeln($message);
    }
}