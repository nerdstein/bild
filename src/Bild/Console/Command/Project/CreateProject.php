<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 *
 */
class CreateProject extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:create')
      ->setDescription('Sets up the project directory from an initial project-template repo');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();

    // Set up new project directory, fail if directory exists.

    // Move bild.yml config to root.

    // Move travis.yml into root (TODO - Add config for Github).

    // Move docs.

    // Move scripts.

    /**
     * Run initial project set up commands.
     */
    // Add repository.
    $output->writeln('<info>Initializing the project repository</info>');
    $command = $this->getApplication()->find('repo:add');

    $command_arguments = array(
      'command' => 'repo:add',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Build initial Composer deps.
    $output->writeln('<info>Loading initial composer dependencies</info>');
    $command = $this->getApplication()
      ->find('project:load-composer-dependencies');

    $command_arguments = array(
      'command' => 'project:load-composer-dependencies',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Futher instructions.
    $output->writeln("<info>Initialization complete. Please configure $this->project_directory/config.yml and then run the InitializeProject command</info>");
  }

}
