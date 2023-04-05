<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:init',
    description: 'init database',
)]
class InitCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln([
            'Initialising Quack App',
            '=======================',
        ]);

        $this->dropDatabase($output);
        $this->createDatabase($output);
        $this->migrateDatabase($output);

        return Command::SUCCESS;


       
    }


    private function dropDatabase(OutputInterface $output, array $args = []): int
    {
        $command = 'doctrine:database:drop';
        $arguments = [
            '--force' => true
        ];
        $arguments = array_merge($arguments, $args);
        return $this->launchCommand($output, $command, $arguments);
    }

    private function createDatabase(OutputInterface $output, array $args = []): int
    {
        $command = 'doctrine:database:create';
        $arguments = [];
        $arguments = array_merge($arguments, $args);
        return $this->launchCommand($output, $command, $arguments);
    }

    private function migrateDatabase(OutputInterface $output, array $args = []): int
    {
        $command = 'doctrine:migrations:migrate';
        $arguments = [
            '--no-interaction' => true
        ];
        $arguments = array_merge($arguments, $args);
        return $this->launchCommand($output, $command, $arguments);
    }

    private function launchCommand(OutputInterface $output, string $commandName, array $arguments): int
    {
        $command = $this->getApplication()->find($commandName);
        $commandInput = new ArrayInput($arguments);
        return $command->run($commandInput, $output);
    }



}
