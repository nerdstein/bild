<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
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
    if ($fs->exists($this->project_directory)) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    }
    $fs->mkdir($this->project_directory);

    // Move bild.yml config to root.
    $dir = pathinfo(__FILE__, PATHINFO_DIRNAME);
    $dir = str_replace('src/Bild/Console/Command/Project', 'config', $dir);
    $fs->copy($dir . '/bild/bild.yml', $this->project_directory);

    // Move travis.yml into root (TODO - Add config for Github).
    $fs->copy($dir . '/travis/.travis.yml', $this->project_directory);

    // Move docs.
    $fs->copy($dir . '/docs', $this->project_directory);

    // Move scripts.
    $fs->copy($dir . '/scripts', $this->project_directory);

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

    // Further instructions.
    $output->writeln("<info>Initialization complete. Please configure $this->project_directory/bild.yml and then run the InitializeProject command. Also, update your project readme.md, docs/technical_architecture.md, travis.yml, and all files under the scripts directory.</info>");
  }

}
