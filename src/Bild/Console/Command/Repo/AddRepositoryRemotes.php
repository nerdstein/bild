<?php

/**
 * @file
 */

namespace Bild\Console\Command\Repo;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 *
 */
class AddRepositoryRemotes extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('repo:add-remotes')
      ->setDescription('Add defined remote repos to the project')
      ->addOption(
        'sudo',
        's',
        InputOption::VALUE_NONE,
        'Run as sudo.'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $sudo = '';
    if ($input->getOption('sudo')) {
      $sudo = 'sudo';
    }

    $fs = new Filesystem();

    // Install .git hooks into the new project.
    if (!empty($this->project_config['git']['remotes'])) {
      // Acquire the existing remotes.
      $remotes = explode("\n", $this->executeProcess("git -C $this->project_directory remote -v", FALSE));
      // Copy the desirable hooks.
      foreach ($this->project_config['git']['remotes'] as $remote_name => $remote_url) {
        $already_exists = FALSE;
        // Skip last line because its a newline.
        for ($i = 0; $i < count($remotes) - 1; $i++) {
          $remote = explode("\t", $remotes[$i]);
          if ($remote[0] == $remote_name) {
            $already_exists = TRUE;
          }
        }

        if (!$already_exists) {
          $output->writeln('<info>Creating Git remote ' . $remote_name . '</info>');
          $this->executeProcess("$sudo git -C $this->project_directory remote add $remote_name $remote_url");
        }
        else {
          $output->writeln('<info>Git remote ' . $remote_name . ' already exists</info>');
        }
      }
    }
  }

}
