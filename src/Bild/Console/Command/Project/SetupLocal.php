<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 *
 */
class SetupLocal extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:setup-local')
      ->setDescription('A manifest to load an existing project on a local machine');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();

    /**
     * Post-configure tasks.
     */

    // Reload Composer dependencies.
    $command = $this->getApplication()
      ->find('project:load-composer-dependencies');

    $command_arguments = array(
      'command' => 'project:load-composer-dependencies',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Set up VM.
    if (!empty($this->project_config['vm'])) {

      // Check VM dependencies.
      $command = $this->getApplication()->find('vm:check-dependencies');

      $command_arguments = array(
        'command' => 'vm:check-dependencies',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);

      // Bootstrap the VM.
      if (!$returnCode and $this->project_config['vm']['bootstrap']) {
        $command = $this->getApplication()->find('vm:bootstrap');

        $command_arguments = array(
          'command' => 'vm:bootstrap',
        );

        $command_input = new ArrayInput($command_arguments);
        $returnCode = $command->run($command_input, $output);
      }
    }

    // Set up Git.
    if (!empty($this->project_config['git'])) {
      // Add remotes, if desired.
      if (!empty($this->project_config['git']['remotes'])) {
        $command = $this->getApplication()->find('repo:add-remotes');

        $command_arguments = array(
          'command' => 'repo:add-remotes',
        );

        $command_input = new ArrayInput($command_arguments);
        $returnCode = $command->run($command_input, $output);
      }
      // Add hooks, if desired.
      if (!empty($this->project_config['git']['hooks'])) {
        $command = $this->getApplication()->find('repo:add-hooks');

        $command_arguments = array(
          'command' => 'repo:add-hooks',
        );

        $command_input = new ArrayInput($command_arguments);
        $returnCode = $command->run($command_input, $output);
      }
    }

    // Set up frontend.
    if (!empty($this->project_config['frontend'])) {
      $command = $this->getApplication()->find('frontend:install');

      $command_arguments = array(
          'command' => 'frontend:install',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Set up aliases.
    if (!empty($this->project_config['project']['alias_file'])) {
      $command = $this->getApplication()->find('project:add-drush-alias');

      $command_arguments = array(
          'command' => 'project:add-drush-alias',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }
    $output->writeln('<info>Local setup has completed.</info>');
  }
}
