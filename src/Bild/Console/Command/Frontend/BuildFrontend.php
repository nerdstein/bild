<?php

/**
 * @file
 */

namespace Bild\Console\Command\Frontend;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Bild\Console\Command\BaseCommand;

/**
 *
 */
class BuildFrontend extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('frontend:build')
      ->setDescription('Build the frontend CSS / JS')
      ->addOption(
        'watch',
        'w',
        InputOption::VALUE_NONE,
        'Use grunt to watch.',
        null
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Get configuration.
    $theme_path = $this->project_directory . $this->project_config['frontend']['theme_path'];

    // Run specific task runner.
    switch ($this->project_config['frontend']['task_runner']) {
      case 'gulp':
        if ($input->getOption('watch')) {
          $this->executeProcess('(cd ' . $theme_path . ' && gulp watch)');
        }
        else {
          $this->executeProcess('(cd ' . $theme_path . ' && gulp sass)');
        }

        break;
      case 'grunt':
        $this->executeProcess('(cd ' . $theme_path . ' && grunt)');
        break;
      case 'sass':
        $this->executeProcess('sass --watch ' . $theme_path . '/sass:stylesheets');
        break;
      case 'compass':
        $this->executeProcess('(cd ' . $theme_path . ' && compass watch --production)');
        break;
    }
  }
}