<?php

/**
 * @file
 */

namespace Bild\Console\Command\Vm;

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
class ConfigureVm extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('vm:configure')
      ->setDescription('Configure DrupalVM, assumes you have run vm:add');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $vm_dir = 'box';
    $fs = new Filesystem();

    // Load the example configuration file included with Drupal VM.
    $parser = new Parser();
    $vm_config = $parser->parse(file_get_contents("{$this->project_directory}/$vm_dir/example.config.yml"));

    // Add the scripts directory to synced folders list.
    $vm_config['vagrant_synced_folders'][] = array(
      'local_path' => "{$this->project_directory}/scripts",
      'destination' => '/scripts',
      'id' => 'project_template_scripts',
      'type' => 'nfs',
    );

    // Add the tests directory to the synced folders list.
    $vm_config['vagrant_synced_folders'][] = array(
      'local_path' => "{$this->project_directory}/tests",
      'destination' => '/tests',
      'id' => 'project_template_tests',
      'type' => 'nfs',
    );

    // Add the tasks directory to the synced folders list.
    $vm_config['vagrant_synced_folders'][] = array(
      'local_path' => "{$this->project_directory}/tasks",
      'destination' => '/tasks',
      'id' => 'project_template_tasks',
      'type' => 'nfs',
    );

    // Use the docroot as the site's primary synced folder.
    $mount_point = "/var/www/{$this->project_config['project']['machine_name']}";
    $vm_config['vagrant_synced_folders'][0]['local_path'] = "{$this->project_directory}/docroot";
    $vm_config['vagrant_synced_folders'][0]['destination'] = $mount_point;

    // Specify that no CRON tasks are setup.
    $vm_config['drupalvm_cron_jobs'] = array(
      array(
        // Provide CRON a name.
        'name' => 'Local Drupal CRON',
        // A duration between CRON tasks.
        'minute' => '*/60',
        // The CRON job to execute.
        'job' => 'drush -r {{ drupal_core_path }} core-cron',
      ),
    );

    // Use the projects key to separate multiple DrupalVM instances.
    $vm_config['vagrant_machine_name'] = $this->project_config['project']['machine_name'];

    // Map the desired IP address for the project.
    $vm_config['vagrant_ip'] = $this->project_config['vm']['vagrant_ip'];

    // Mimic Acquia Cloud configuration.
    $vm_config['vagrant_box'] = 'geerlingguy/ubuntu1204';
    $vm_config['php_version'] = '5.5';
    $vm_config['solr_version'] = '4.5.1';

    // Specify the VM extras you wish you install for this project.
    $vm_config['installed_extras'] = $this->project_config['vm']['installed_extras'];

    // Specify project specific global Composer packages.
    $vm_config['composer_global_packages'] = $this->project_config['vm']['composer_global_packages'];

    // Update domain configuration.
    $local_url = parse_url($this->project_config['project']['local_url']);
    $vm_config['vagrant_hostname'] = $local_url['host'];
    $vm_config['drupal_domain'] = $local_url['host'];
    $vm_config['drupal_site_name'] = $this->project_config['project']['human_name'];
    $vm_config['drupal_core_path'] = $mount_point;
    $vm_config['drupal_major_version'] = $this->project_config['vm']['drupal_major_version'];

    // Set apache vhosts by project, not to DrupalVM default.
    $vm_config['apache_vhosts'][1]['servername'] = 'xhprof.' . $local_url['host'];
    $vm_config['apache_vhosts'][2]['servername'] = 'pimpmylog.' . $local_url['host'];

    // Update the path to make file.
    $make_file = $this->project_config['project']['make_file'];
    $vm_config['drush_makefile_path'] = '/scripts/' . $make_file;
    // Remove makefile extension.
    $vm_config['drupal_install_profile'] = $this->project_config['project']['install_profile'];

    // Update other important settings.
    $vm_config['drupal_enable_modules'] = [];
    $vm_config['extra_apt_packages'] = [];

    // Do not execute subsequent drush make within the VM since files are in docroot.
    $vm_config['build_makefile'] = FALSE;
    $vm_config['install_site'] = TRUE;

    // Set the installed version of drush.
    $vm_config['drush_version'] = $this->project_config['vm']['drush_version'];

    // Write adjusted config.yml to disk.
    $fs->dumpFile("$this->project_directory/$vm_dir/config.yml", Yaml::dump($vm_config, 4, 2));

    $output->writeln("<info>Drupal VM was installed to `$this->project_directory/box`.</info>");
  }

}
