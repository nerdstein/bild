<?php

/**
 * @file
 */

namespace Bild\Console\Command\Frontend;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bild\Console\Command\BaseCommand;

/**
 *
 */
class InitializeFrontendTools extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('frontend:install')
      ->setDescription('Installs the tools needed for the frontend');

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    // Install bundler gems.
    $theme_path = $this->project_directory . $this->project_config['frontend']['theme_path'];
    //$gemfile_path = $theme_path . '/' . $this->project_config['frontend']['gemfile'];
    //$this->executeProcess('gem install bundler');
    //$this->executeProcess('bundle install --path=' . $theme_path . '  --gemfile ' . $gemfile_path);

    // Install specific task runner.
    switch ($this->project_config['frontend']['task_runner']) {
      case 'gulp':
        if ($this->executeProcess('which npm')=='') {
          throw new \RuntimeException('Please install Node and NPM on your local system');
        }
        $this->executeProcess('npm install gulp -g');
        $this->executeProcess('(cd ' . $theme_path . ' && npm install)');
        $this->executeProcess('npm install --save-dev --prefix=' . $theme_path . ' gulp');
        break;
      case 'grunt':
        $this->executeProcess('npm install --save-dev --prefix=' . $theme_path . ' grunt-cli');
        break;
      case 'sass':
        $this->executeProcess('gem install sass');
        break;
      case 'compass':
        $this->executeProcess('gem install compass');
        break;
    }
  }
}