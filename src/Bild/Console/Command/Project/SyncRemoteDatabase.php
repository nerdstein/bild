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
class SyncRemoteDatabase extends Command {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:sync-remote-database')
      ->setDescription('Sync remote database between sites')
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

    $output->writeln('<info>Syncing databases between sites...</info>');
    $remote_alias = $input->getArgument('remote-alias');
    $target_alias = $input->getArgument('target-alias');

    // TODO - Consider backing up first?
    $output->writeln('<info>Dropping existing target database</info>');
    $this->executeProcess($this->bin . "/drush $target_alias sql-drop --yes", TRUE, $this->project_directory);

    $output->writeln('<info>Retrieving remote db, note: you may be prompted for your SSH password</info>');
    $this->executeProcess($this->bin . "/drush $remote_alias $target_alias --yes --structure-tables-key=lightweight", TRUE, $this->project_directory);

    $output->writeln('<info>Clearing cache</info>');
    $this->executeProcess($this->bin . "/drush $target_alias cr", TRUE, $this->project_directory);

    $output->writeln('<info>Running database updates against target codebase</info>');
    $this->executeProcess($this->bin . "/drush $target_alias updb --yes", TRUE, $this->project_directory);
  }
}
