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
class ValidatePHPMD extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate-phpmd')
      ->setDescription('Validate PHPMD on codebase');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $files = $this->getValidationFiles();
    $extensions = $this->project_config['validation']['extensions'];
    $extensions = implode(',', $extensions);

    foreach ($files as $dir) {
      $cmd = "{$this->bin}/phpmd $dir text codesize,unusedcode,cleancode --suffixes $extensions";
      $this->executeProcess($cmd);
    }
  }
}
