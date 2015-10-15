<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 *
 */
class CreateProject extends Command {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:create')
      ->setDescription('Sets up the project directory from an initial project-template repo')
      ->addArgument(
          'project-dir',
          InputArgument::REQUIRED,
          'Which directory does your project reside?'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();
    $project_directory = $input->getArgument('project-dir');

    // Set up new project directory, fail if directory exists.
    if ($fs->exists($project_directory)) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    }
    $fs->mkdir($project_directory);

    // Move bild.yml config to root.
    $dir = pathinfo(__FILE__, PATHINFO_DIRNAME);
    $dir = str_replace('src/Bild/Console/Command/Project', 'defaults', $dir);
    $fs->copy($dir . '/config/bild.yml', $project_directory . '/bild.yml');

    // Move travis.yml into root (TODO - Add config for Github).
    $fs->copy($dir . '/config/.travis.yml', $project_directory . '/.travis.yml');

    // Move and load composer.
    $fs->copy($dir . '/config/composer.json', $project_directory . '/composer.json');
    $process = new Process("composer install");
    $process->setTimeout(3600);
    $process->setWorkingDirectory($project_directory);
    $process->run();

    // Move docs.
    $fs->mirror($dir . '/docs', $project_directory . '/docs');

    // Move scripts.
    $fs->mirror($dir . '/scripts', $project_directory . '/scripts');

    // Move tests.
    $fs->mirror($dir . '/tests', $project_directory . '/tests');

    /**
     * Run initial project set up commands.
     */
    // Add repository.
    $output->writeln('<info>Initializing the project repository</info>');
    $command = $this->getApplication()->find('repo:add');

    $command_arguments = array(
      'command' => 'repo:add',
      '--project-dir' => $project_directory,
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Build initial Composer deps.
    $output->writeln('<info>Loading initial composer dependencies</info>');
    $command = $this->getApplication()
      ->find('project:load-composer-dependencies');

    $command_arguments = array(
      'command' => 'project:load-composer-dependencies',
      '--project-dir' => $project_directory,
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Further instructions.
    $output->writeln("<info>Initialization complete. Please configure $project_directory/bild.yml and then run the InitializeProject command. Also, update your project readme.md, docs/technical_architecture.md, travis.yml, and all files under the scripts directory.</info>");
  }

}
