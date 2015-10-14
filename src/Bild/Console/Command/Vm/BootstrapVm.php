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

/**
 *
 */
class BootstrapVm extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('vm:bootstrap')
      ->setDescription('Loads the DrupalVM on your system. Assumes you have run add and check-dependency commands.');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $vm_dir = 'box';

    // If all dependencies are met, load the VM.
    $output->writeln('<info>Bootstrapping Drupal VM...</info>');

    // Load ansible reqs.
    if (!empty($this->project_config['vm']['rebuild_requirements']) and $this->project_config['vm']['rebuild_requirements']) {
      $output->writeln('<info>Loading ansible requirements. NOTE - you will be prompted to enter your sudo password</info>');
      $role_file = $this->project_directory . '/' . $vm_dir . '/provisioning/requirements.txt';
      $result = strtolower($this->executeProcess('sudo ansible-galaxy install --force --role-file=' . $role_file));
    }

    // Load host manager.
    $output->writeln('<info>Loading host manager</info>');
    $result = strtolower($this->executeProcess('vagrant plugin install vagrant-hostsupdater'));

    // Run Vagrant up from VM dir.
    $output->writeln('<info>Bootstrapping VM</info>');
    $result = strtolower($this->executeProcess('vagrant up', TRUE, $this->project_directory . '/' . $vm_dir));
  }

}
