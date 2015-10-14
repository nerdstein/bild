<?php

/**
 * @file
 * Deploys project code and build artifacts to a repo.
 *
 * This is triggered in travis if a release is tagged in github.
 *
 * If a release is tagged with a -dev or -rc then the 'integration-branch' will
 * be tagged and pushed up to the target repo. Otherwise the 'prod-branch' will
 * be tagged and pushed up to the target repo.
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class SyncRepos extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:sync')
      ->setDescription('Deploy codebase and build artifacts.')
      ->addArgument(
        'target-repo',
        InputArgument::REQUIRED,
        'Which repo is the sync?'
      )
      ->addArgument(
        'target-branch',
        InputArgument::REQUIRED,
        'Which branch on the target?'
      );

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $target_repo = $input->getArgument('target-repo');
    $target_branch = $input->getArgument('target-branch');

    if (empty($this->project_config['git']['remotes'][$target_repo])) {
      $output->writeln('<error>Target repo not found in config.yml for project</error>');
      throw new \RuntimeException;
    }
    $target_repo_name = $this->project_config['git']['remotes'][$target_repo];

    $output->writeln("<info>Syncing to $target_repo_name...</info>");

    $ret = $this->executeProcess("git push $target_repo $target_branch", TRUE, $this->project_directory);
  }

}
