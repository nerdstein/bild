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
class CheckVmDependencies extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('vm:check-dependencies')
      ->setDescription('Ensure your system has the tools needed to run DrupalVM');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // We need to do a full audit before returning. As such, we should check for errors more broadly.
    $has_errors = FALSE;

    $output->writeln('<info>Checking the Drupal VM dependencies...</info>');

    // Check for virtualbox version 4.3.x.
    $output->writeln('<info>Checking for virtualbox</info>');
    $result = strtolower($this->executeProcess('VBoxManage -v'));
    if (strpos($result, 'vboxmanage: command not found')) {
      $output->writeln('<error>Unmet dependency, please install virtualbox 4.3.x</error>');
      throw new \RuntimeException('Virtualbox is missing');
    }
    else {
      $parsed_version = explode(".", $result);
      // Check major and minor version.
      if ($parsed_version[0] != '5' and $parsed_version[1] != '0') {
        $output->writeln('<comment>Unmet dependency, please upgrade virtualbox to version 5.0.x</comment>');
        $has_errors = TRUE;
      }
      else {
        $output->writeln('<info>Virtualbox version is currently supported.</info>');
      }
    }

    // Check for vagrant 1.7.2 or higher.
    $output->writeln('<info>Checking for vagrant</info>');
    $result = strtolower($this->executeProcess('vagrant -v'));
    if (strpos($result, 'vagrant: command not found')) {
      $output->writeln('<error>Unmet dependency, please install vagrant 1.7.2 or higher</error>');
      $has_errors = TRUE;
    }
    else {
      $parsed_version = explode(' ', $result);
      $parsed_version = explode(".", $parsed_version[1]);
      // Check major and minor version.
      if ($parsed_version[0] != '1' and $parsed_version[1] != '7' and intval($parsed_version[2]) > 2) {
        $output->writeln('<error>Unmet dependency, please upgrade vagrant to version 1.7.2 or higher</error>');
        $has_errors = TRUE;
      }
      else {
        $output->writeln('<comment>Vagrant version is currently supported.</comment>');
      }
    }

    // Check for ansible version 1.9.2 or higher.
    $result = strtolower($this->executeProcess('ansible --version'));
    if (strpos($result, 'ansible: command not found')) {
      $output->writeln('<error>Unmet dependency, please install ansible 1.9.2 or higher. To install, run `sudo pip install ansible`.</error>');
      $has_errors = TRUE;
    }
    else {
      $parsed_version = explode(' ', $result);
      $parsed_version = explode(".", $parsed_version[1]);
      // Check major and minor version.
      if ($parsed_version[0] != '1' and $parsed_version[1] != '9' and intval($parsed_version[2]) > 2) {
        $output->writeln('<error>Unmet dependency, please install ansible 1.9.2 or higher. To upgrade, run `sudo pip install ansible -U`.</error>');
        $has_errors = TRUE;
      }
      else {
        $output->writeln('<comment>Ansible version is currently supported.</comment>');
      }
    }

    // Check for duplicate machine names and duplicate IP within VirtualBox.
    $output->writeln('<info>Checking for duplicated virtualbox host names and IPs</info>');
    $result = $this->executeProcess('vboxmanage list vms', FALSE);
    $existing_hosts = explode("\n", strtolower($result));
    $local_url = parse_url($this->project_config['project']['local_url']);
    foreach ($existing_hosts as $existing_host) {
      if ($existing_host) {
        $host_name = explode(" ", $existing_host);
        $host_name = str_replace('"', '', $host_name[0]);
        // Check host.
        if (strtolower($host_name) == strtolower($local_url['host'])) {
          $output->writeln('<error>You already have a virtual machine host with the name ' . $host_name . '.</error>');
          $output->writeln('<comment>You will not be able to run these virtual machines concurrently. Please update the local_url configuration to use something unique.</comment>');
          $has_errors = TRUE;
        }

        // Load the IP(s) that are associated to the existing VMs.
        $result = $this->executeProcess('vboxmanage guestproperty enumerate ' . $host_name, FALSE);
        if ($result) {
          $host_ips = explode("\n", strtolower($result));
          foreach ($host_ips as $host_ip) {
            // $output->writeln('<comment>Checking \'' . $host_ip . '\'</comment>');
            if ($host_ip and strpos($host_ip, '/v4/ip')) {
              // Key/values are comma-separated.
              $ip_address = explode(", ", $host_ip);
              // The IP address value is stored in the second value in the form of 'value: xxx.yyy.zzz.qqq'.
              // Split the key from the value.
              $ip_address = explode(": ", $ip_address[1]);
              // Grab the value itself.
              $ip_address = $ip_address[1];

              // Check it against the specified IP.
              if ($ip_address == $this->project_config['vm']['vagrant_ip']) {
                $output->writeln('<error>The ' . $host_name . ' virtual machine host already has the IP ' . $ip_address . '.</error>');
                $output->writeln('<comment>You will not be able to run these virtual machines concurrently. Please update the vm > vagrant_ip configuration to use something unique.</comment>');
                $has_errors = TRUE;
              }
            }
          }
        }
      }
    }

    // Print out message to the user to review the output generated above.
    if ($has_errors) {
      $output->writeln('<error>Errors were generated during the installation process. Please review the errors and manually bootstrap the VM.</error>');
      $output->writeln("<info>To set up the Drupal VM, follow the Quick Start Guide at http://www.drupalvm.com</info>");
    }

    // Return whether or not there were issues found that would impede the VM.
    throw new \RuntimeException('Virtualbox is missing');
  }

}
