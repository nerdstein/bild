<?php

/**
 * @file
 */

namespace Bild\Console\Command\Testing;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 *
 */
class RunAutomatedTests extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:automated-tests')
      ->setDescription('Executes full suite of automated tests')
      ->addOption(
        'travis',
        't',
        InputOption::VALUE_NONE,
        'Is this a travis build?'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    // Test security updates.
    if (array_key_exists('security-updates', $this->project_config['automated-tests'])) {
      $output->writeln('<info>Running security update test</info>');
      $command = $this->getApplication()->find('testing:security-update');

      $command_arguments = array(
        'command' => 'testing:security-updates',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Test Behat.
    if (array_key_exists('behat', $this->project_config['automated-tests'])) {
      $output->writeln('<info>Running Behat tests</info>');
      $command = $this->getApplication()->find('testing:behat');

      $command_arguments = array(
        'command' => 'testing:behat',
      );

      if ($input->getOption('travis')) {
        $command_arguments = array(
          'command' => 'testing:behat',
          'behat-target' => 'travis.yml',
          'behat-profile' => 'travis',
        );
      }

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Test PHPUnit.
    if (array_key_exists('phpunit', $this->project_config['automated-tests'])) {
      $output->writeln('<info>Running PHPUnit tests</info>');
      $command = $this->getApplication()->find('testing:phpunit');

      $command_arguments = array(
        'command' => 'testing:phpunit',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }
  }
}
