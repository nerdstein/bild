<?php

/**
 * @file
 */

namespace Bild\Console\Command\Initialize;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Bild\Console\Command\BaseCommand;

/**
 *
 */
class AddDrushAliases extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:add-drush-alias')
      ->setDescription('Add drush aliases');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();

    // Install drush aliases into the new project.
    $output->writeln('<info>Copying drush alias file</info>');

    $alias = $this->project_config['project']['machine_name'] . '.aliases.drushrc.php';

    // Get Home directory.
    $cmd = 'echo ~';
    $home = trim($this->executeProcess($cmd));

    $fs->copy($this->project_directory . '/scripts/aliases.drushrc.php', "$home/.drush/" . $alias, FALSE);

    $output->writeln("<info>$alias copied to $home/.drush</info>");

    $output->writeln("<info>$home</info>");
  }

}
