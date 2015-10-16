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
class DestroyVm extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('vm:destroy')
      ->setDescription('Remove the DrupalVM')
      ->addArgument(
          'remove-files',
          InputArgument::OPTIONAL,
          'Do you want to remove the VM configuration files as well as the VM?'
      );

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();
    $remove_files = $input->getArgument('remove-files');

    $output->writeln('<info>Removing the Drupal VM...</info>');

    // Add Drupal VM Vagrant box repository and then remove the .git files.
    $vm_dir = 'box';
    $result = strtolower($this->executeProcess('vagrant destroy --force', TRUE, $this->project_directory . '/' . $vm_dir));

    if ($remove_files) {
      $fs->remove("$this->project_directory/$vm_dir");
    }

  }
}
