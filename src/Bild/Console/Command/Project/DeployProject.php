<?php

/**
 * @file
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
class DeployProject extends BaseCommand {

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:deploy')
      ->setDescription('Deploy codebase and build artifacts.')
      ->addArgument(
        'alias',
        InputArgument::REQUIRED,
        'Drush alias.'
      )
      ->addArgument(
        'reference',
        InputArgument::REQUIRED,
        'What git reference do you want to deploy? (tag, branch, hash)'
      )
      ->addOption(
        'site-install',
        'si',
        InputOption::VALUE_NONE,
        'Install the site?',
        null
      );

  }

  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $alias = $input->getArgument('alias');
    $reference = $input->getArgument('reference');
    $drush = $this->bin . '/drush';

    $output->writeln("<info>Deploy $reference to $alias.</info>");
    $task = $this->executeProcess("$drush $alias ac-code-path-deploy $reference 2>&1", FALSE, $this->project_directory);

    $re = "/Task (\\d+) started./";

    preg_match_all($re, $task, $task_id);
    $task_id = $task_id[1][0];
    $task_completed = FALSE;

    $count = 0;
    while (!$task_completed) {
      sleep(5);
      $cmd = "$drush $alias ac-task-info $task_id";

      $task = $this->executeProcess($cmd, TRUE, $this->project_directory);

      $task_completed = stristr($task, 'state         :  done');

      if ($count > 4) {
        $output->writeln('<error>Code deploy did not complete.</error>');
        throw new \RuntimeException;
      }
      $count++;
    }
    $output->writeln("<info>Code deploy finished.</info>");

    if ($input->getOption('site-install')) {
      $output->writeln("<info>Install site on $alias.</info>");
      $command = $this->getApplication()->find('project:install');

      $command_arguments = array(
        'command' => 'project:install',
        '--site-alias' => $alias,
      );


      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }
  }

}