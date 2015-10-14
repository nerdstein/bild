<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class SyncRemoteFiles extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:sync-remote-files')
      ->setDescription('Sync remote files between sites')
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

    $output->writeln('<info>Syncing files between sites...</info>');

    $remote_alias = $input->getArgument('remote-alias');
    $target_alias = $input->getArgument('target-alias');

    // TODO - Consider backing up first?
    $output->writeln('<info>Executing rsync, note this may prompt you for your SSH password</info>');
    $this->executeProcess($this->bin . "/drush rsync $remote_alias:%files/ $target_alias:%files", TRUE, $this->project_directory);
  }
}
