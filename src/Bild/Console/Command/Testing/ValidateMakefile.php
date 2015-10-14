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
class ValidateMakefile extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate-makefile')
      ->setDescription('Validate makefile on codebase');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $project_make_file = $this->project_config['project']['make_file'];

    // See https://www.drupal.org/project/drupalorg_drush.
    $this->executeProcess($this->bin . "/drush dl drupalorg_drush");
    $this->executeProcess($this->bin . "/drush cache-clear drush");
    $this->executeProcess($this->bin . "/drush validate-makefile {$this->project_directory}/$project_make_file");
  }
}
