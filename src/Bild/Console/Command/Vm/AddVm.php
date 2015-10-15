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

/**
 *
 */
class AddVm extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('vm:add')
      ->setDescription('Add the files for the DrupalVM');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();

    $output->writeln('<info>Cloning Drupal VM from GitHub...</info>');

    // Add Drupal VM Vagrant box repository and then remove the .git files.
    $vm_dir = 'box';

    $fs->remove("$this->project_directory/$vm_dir");
    // We are intentionally pinning to a specific release for stability.
    $this->executeProcess("git clone --branch 2.1.1 git@github.com:geerlingguy/drupal-vm.git $this->project_directory/$vm_dir");
    $fs->remove("$this->project_directory/$vm_dir/.git");

  }
}
