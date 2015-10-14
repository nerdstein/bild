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

/**
 *
 */
class TestSecurityUpdates extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:security-updates')
      ->setDescription('Check for Drupal security updates');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->executeProcess("! "  . $this->bin . "/drush -n ups --check-disabled --security-only 2>/dev/null | grep 'SECURITY UPDATE'", TRUE, "{$this->project_directory}/docroot");
  }
}
