<?php

/**
 * @file
 */

namespace Bild\Console\Command\Travis;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 *
 */
class InitializeTravis extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('travis:initialize')
      ->setDescription('Initializes a TravisCI build');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    // Run Drush Make command to rebuild docroot.
    $output->writeln('<info>Building the code base</info>');
    $command = $this->getApplication()->find('project:rebuild-site');

    $command_arguments = array(
      'command' => 'project:rebuild-site',
    );

    $command_input = new ArrayInput($command_arguments);
    // Don't run rebuild site while we are tracking dev releases.
    //$returnCode = $command->run($command_input, $output);


    // Build out the front-end.
    $output->writeln('<info>Initialize front-end tools</info>');
    $command = $this->getApplication()->find('frontend:install');

    $command_arguments = array(
      'command' => 'frontend:build',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    $output->writeln('<info>Building the front-end</info>');
    $command = $this->getApplication()->find('frontend:build');

    $command_arguments = array(
    'command' => 'frontend:build',
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Invoke site install.
    $output->writeln('<info>Installing the site</info>');
    $command = $this->getApplication()->find('project:install');

    $command_arguments = array(
      'command' => 'project:install',
      '--site-dir' => 'travisci.local',
      '--db-url' => 'mysql://root@127.0.0.1/travis_ci_' . $this->project_config['project']['machine_name'],
    );

    $command_input = new ArrayInput($command_arguments);
    $returnCode = $command->run($command_input, $output);

    // Set owner:group for all project files.
    $cmd = "sudo chown -R www-data:www-data $this->project_directory";
    $this->executeProcess($cmd);

    // Setup Git.
    $this->executeProcess("sudo git config --global user.email 'travis@example.com'", TRUE, $this->project_directory);
    $this->executeProcess("sudo git config --global user.name 'Travis'", TRUE, $this->project_directory);
  }

}
