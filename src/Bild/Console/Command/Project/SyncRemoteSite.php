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

/**
 *
 */
class SyncRemoteSite extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:sync-remote-site')
      ->setDescription('Sync remote database and files between sites')
      ->addArgument(
        'remote-alias',
        InputArgument::REQUIRED,
        'Which drush alias do you wish to pull from?'
      )
      ->addArgument(
        'target-alias',
        InputArgument::REQUIRED,
        'Which drush alias do you wish to deploy to?'
      );

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $output->writeln('<info>Syncing from remote site...</info>');

    $remote_alias = $input->getArgument('remote-alias');
    $target_alias = $input->getArgument('target-alias');

    // Run database.
    $command = $this->getApplication()->find('project:sync-remote-database');

    $command_arguments = array(
      'command' => 'project:sync-remote-database',
      'remote-alias' => $remote_alias,
      'target-alias' => $target_alias,
    );

    $command_input = new ArrayInput($command_arguments);
    try {
      $command->run($command_input, $output);
    }
    catch (\RuntimeException $e) {
      $passed = FALSE;
    }

    // Run files.
    $command = $this->getApplication()->find('project:sync-remote-files');

    $command_arguments = array(
      'command' => 'project:sync-remote-files',
      'remote-alias' => $remote_alias,
      'target-alias' => $target_alias,
    );

    $command_input = new ArrayInput($command_arguments);
    try {
      $command->run($command_input, $output);
    }
    catch (\RuntimeException $e) {
      $passed = FALSE;
    }
  }
}
