<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Bild\Console\Command\BaseCommand;

/**
 *
 */
class BuildMakeFile extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:build-make-file')
      ->setDescription('Add the files for the DrupalVM')
      ->addArgument(
        'with-core',
        InputArgument::OPTIONAL,
        'Which directory does your project reside?'
      )
      ->addArgument(
        'working-copy',
        InputArgument::OPTIONAL,
        'Do you want working copies of the repos?'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $with_core = $input->getArgument('with-core');
    $working_copy = $input->getArgument('working-copy');
    $fs = new Filesystem();

    $drush = $this->bin . '/drush';
    $this->executeProcess("$drush cc drush");

    // Start the command.
    if ($with_core) {
      $command = "$drush make ";
    }
    else {
      $command = "$drush make --no-core ";
    }

    if ($working_copy) {
      $command .= "--working-copy ";
    }

    // Specify the location of the make file.
    $command .= $this->project_directory . '/' . $this->project_config['project']['make_file'];

    // Specify where the project gets made.
    $command .= ' ' . $this->project_directory . '/docroot -y';

    // Run the command.
    $output->writeln("<info>Running Drush Make into your project docroot...</info>");
    $output->write($command);
    $this->executeProcess($command);
  }

}
