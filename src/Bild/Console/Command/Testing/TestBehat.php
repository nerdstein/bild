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
class TestBehat extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:behat')
      ->setDescription('Run the projects behat tests')
      ->addArgument(
        'behat-target',
        InputArgument::OPTIONAL,
        'What is your target behat config file?',
        'default.yml'
      )
      ->addArgument(
        'behat-profile',
        InputArgument::OPTIONAL,
        'What is your target behat profile?',
        'default'
      );

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $behat_config = $input->getArgument('behat-target');
    $behat_profile = $input->getArgument('behat-profile');

    $cmd = "{$this->bin}/behat -c {$this->project_directory}/tests/behat/$behat_config -p $behat_profile";

    $output->writeln("<info>$cmd</info>");
    $output->writeln("{$this->project_directory}/tests/behat");
    $this->executeProcess($cmd, TRUE, "{$this->project_directory}/tests/behat");
  }

}
