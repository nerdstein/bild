<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Parser;

/**
 *
 */
class ConfigureTestingFramework extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:configure-testing')
      ->setDescription('Configure automated tests');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();

    // Create local Behat configuration.
    $output->writeln("<info>Configuring Local Behat yml files...</info>");

    $parser = new Parser();
    $behat_config = $parser->parse(file_get_contents("{$this->project_directory}/tests/behat/example.local.yml"));

    $behat_config['local']['extensions']['Drupal\DrupalExtension']['drupal']['drupal_root'] = "{$this->project_directory}/docroot";
    $behat_config['local']['extensions']['Behat\MinkExtension']['base_url'] = $this->project_config['project']['local_url'];
    $behat_config['local']['extensions']['Behat\MinkExtension']['javascript_session'] = $this->project_config['testing_framework']['behat']['javascript_driver'];

    // Write adjusted config.yml to disk.
    $fs->dumpFile("{$this->project_directory}/tests/behat/local.yml", Yaml::dump($behat_config, 4, 2));

    $output->writeln("<info>Configuring Drupal VM Behat yml files...</info>");

    // Create VM-specific Behat configuration.
    $parser = new Parser();
    $behat_config = $parser->parse(file_get_contents("{$this->project_directory}/tests/behat/example.vm.yml"));
    $behat_config['vm']['extensions']['Drupal\DrupalExtension']['drupal']['drupal_root'] = "/var/www/{$this->project_config['project']['machine_name']}";
    $behat_config['vm']['extensions']['Behat\MinkExtension']['base_url'] = $this->project_config['project']['local_url'];
    $behat_config['vm']['extensions']['Behat\MinkExtension']['javascript_session'] = $this->project_config['testing_framework']['behat']['javascript_driver'];

    // Write adjusted config.yml to disk.
    $fs->dumpFile("{$this->project_directory}/tests/behat/vm.yml", Yaml::dump($behat_config, 4, 2));
  }
}
