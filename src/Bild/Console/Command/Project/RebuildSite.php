<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 *
 */
class RebuildSite extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:rebuild-site')
      ->setDescription('Rebuilds your project site');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $output->writeln('<info>Rebuilding the site...</info>');

    $fs = new Filesystem();

    if ($fs->exists($this->project_directory . '/docroot')) {
      // Updating permissions if anything has been locked down.
      $fs->chmod($this->project_directory . '/docroot', 0777, 0000, TRUE);

      // Removing existing docroot.
      $fs->remove($this->project_directory . '/docroot');
    }

    // Run make command with core.
    $command = $this->getApplication()->find('project:build-make-file');

    $command_arguments = array(
      'command' => 'project:build-make-file',
      'with-core' => TRUE,
    );

    $command_input = new ArrayInput($command_arguments);
    $return_code = $command->run($command_input, $output);
    $output->writeln('<info>Make file has completed</info>');

    // Apply patch for custom directories.
    $output->writeln('<info>Bring back custom files.</info>');

    $checkout = implode(' ', $this->project_config['project']['custom_directories']);

    $this->executeProcess("git checkout $checkout", TRUE, $this->project_directory);

    $output->writeln('<info>Rebuild site completed.</info>');

  }
}
