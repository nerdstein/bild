<?php

/**
 * @file
 */

namespace Bild\Console\Command\Testing;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class RunValidationTests extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate')
      ->setDescription('Executes full validation tests');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    // Validate linting.
    if (array_key_exists('phplint', $this->project_config['validation'])) {
      $output->writeln('<info>Validating linting</info>');
      $command = $this->getApplication()->find('testing:validate-phpsyntax');

      $command_arguments = array(
        'command' => 'validate-phpsyntax',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Validate phpcs.
    if (array_key_exists('phpcs', $this->project_config['validation'])) {
      $output->writeln('<info>Validating phpcs</info>');
      $command = $this->getApplication()->find('testing:validate-phpcs');

      $command_arguments = array(
        'command' => 'testing:validate-phpcs',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Validate phpmd.
    if (array_key_exists('phpmd', $this->project_config['validation'])) {
      $output->writeln('<info>Validating phpmd</info>');
      $command = $this->getApplication()->find('testing:validate-phpmd');

      $command_arguments = array(
        'command' => 'testing:validate-phpmd',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Validate makefile.
    if (array_key_exists('makefile', $this->project_config['validation'])) {
      $output->writeln('<info>Validating make file</info>');
      $command = $this->getApplication()->find('testing:validate-makefile');

      $command_arguments = array(
        'command' => 'testing:validate-makefile',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }
  }
}
