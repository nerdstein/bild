<?php

/**
 * @file
 */

namespace Bild\Console\Command\Repo;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class AddRepository extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('repo:add')
      ->setDescription('Add the repo to the project')
      ->addArgument(
        'developer-fork',
        InputArgument::OPTIONAL,
        'What is your project fork?'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Make the new project a repo.
    $developer_fork = $input->getArgument('developer-fork');
    $output->writeln('<info>Initializing new project git repository</info>');
    $this->executeProcess('git init', FALSE, $this->project_directory);

    // Optionally, add the remote for the individual's fork.
    if ($developer_fork) {
      $output->writeln("<info>Adding your project fork as origin repository.</info>");
      $this->executeProcess("git remote add origin $developer_fork", FALSE, $this->project_directory);
    }
  }
}
