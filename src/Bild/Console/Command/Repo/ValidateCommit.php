<?php

/**
 * @file
 */

namespace Bild\Console\Command\Repo;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 *
 */
class ValidateCommit extends BaseCommand {

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('repo:validate-commit')
      ->setDescription('Validate a commit');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $output->writeln("<info>Files changed in commit...</info>");

    $cmd = 'git diff --cached --name-only --diff-filter=ACMR HEAD';
    $files = explode("\n", $this->executeProcess($cmd, TRUE, $this->project_directory));

    if (empty($files)) {
      $output->writeln("<info>No files to check.</info>");
      return;
    }

    // Check each file.
    $passed = TRUE;
    foreach ($files as $file) {

      // Verify extension.
      if ($this->checkValidationFile($file)) {

        // Run black list checking.
        $command = $this->getApplication()->find('testing:validate-blacklist');

        $command_arguments = array(
          'command' => 'testing:validate-blacklist',
          'file-path' => $file,
        );

        $command_input = new ArrayInput($command_arguments);
        try {
          $command->run($command_input, $output);
        }
        catch (\RuntimeException $e) {
          $output->writeln("<error>$file failed a blacklist.</error>");
          $passed = FALSE;
        }

        // Run phpcs checking.
        $command = $this->getApplication()->find('testing:validate-phpcs');

        $command_arguments = array(
          'command' => 'testing:validate-phpcs',
          'file-path' => $file,
        );

        $command_input = new ArrayInput($command_arguments);
        try {
          $command->run($command_input, $output);
        }
        catch (\RuntimeException $e) {
          // Run code beautifier.
          $command = $this->getApplication()->find('project:run-phpcbf');

          $command_arguments = array(
            'command' => 'project:run-phpcbf',
            'file-path' => $file,
            'git-add' => TRUE,
          );

          $command_input = new ArrayInput($command_arguments);

          $command->run($command_input, $output);

          // Re-run phpcs checking.
          $command = $this->getApplication()->find('testing:validate-phpcs');

          $command_arguments = array(
            'command' => 'testing:validate-phpcs',
            'file-path' => $file,
          );

          $command_input = new ArrayInput($command_arguments);

          try {
            $command->run($command_input, $output);
          }
          catch (\RuntimeException $e) {
            $passed = FALSE;
            $output->writeln("<error>$file failed a second PHPCS check after running PHPCBF.</error>");
          }
        }
      }
    }

    if (!$passed) {
      throw new \RuntimeException("This commit cannot pass due to failed tests.");
    }
  }

}
