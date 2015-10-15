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
class InitializeProject extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:initialize')
      ->setDescription('A manifest to initially build a configured project');

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

    // Set up testing framework.
    if (!empty($this->project_config['testing_framework'])) {

      $command = $this->getApplication()->find('project:configure-testing');

      $command_arguments = array(
        'command' => 'project:configure-testing',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    if (!empty($this->project_config['docs'])) {
      // Initialize the desired project documents.
      $command = $this->getApplication()
        ->find('project:initialize-documentation');

      $command_arguments = array(
        'command' => 'project:initialize-documentation',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);
    }

    // Remake the project.
    $command = $this->getApplication()->find('project:build-make-file');

    $command_arguments = array(
      'command' => 'project:build-make-file',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Set up VM.
    if (!empty($this->project_config['vm'])) {
      // Add the VM.
      $command = $this->getApplication()->find('vm:add');

      $command_arguments = array(
        'command' => 'vm:add',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);

      // Sync project configuration to the VM.
      $command = $this->getApplication()->find('vm:configure');

      $command_arguments = array(
        'command' => 'vm:configure',
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);

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
  }

}
