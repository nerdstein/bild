<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


/**
 *
 */
class InstallSite extends BaseCommand {

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:install')
      ->setDescription('Installs site, assumes you have run vm:add')
      ->addOption(
        'site-alias',
        'sa',
        InputOption::VALUE_REQUIRED,
        'Which Drush alias to you wish to run the installation?',
        null
      )
      ->addOption(
        'site-dir',
        'dir',
        InputOption::VALUE_REQUIRED,
        'Which is the site directory?',
        null
      )
      ->addOption(
        'db-url',
        'db',
        InputOption::VALUE_REQUIRED,
        'Which is the database url?',
        null
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $site_alias = $input->getOption('site-alias');
    $site_dir = $input->getOption('site-dir');
    $db_url = $input->getOption('db-url');

    $profile = $this->project_config['project']['install_profile'];
    $site_name = $this->project_config['project']['human_name'];

    $output->writeln('<info>Installing site...</info>');

    // Use locally installed drush if there is a collision between drop and
    // Drupal's vendor files.

    if ($site_alias) {
      $cmd = $this->bin . '/drush $site_alias site-install';
    } else {
      $cmd = $this->bin . '/drush site-install';
    }

    if ($site_dir) {
      $cmd .= " --sites-subdir='$site_dir'";
    }

    $cmd .= " $profile -y --site-name='$site_name' --account-name=admin --account-pass=admin";

    if ($db_url) {
      $cmd .= " --db-url=$db_url";
    }

    $this->executeProcess($cmd, TRUE, $this->project_directory . '/docroot');

    $output->writeln("<info>$site_name installed.</info>");
  }

}
