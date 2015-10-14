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
class AddRepositoryHooks extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('repo:add-hooks')
      ->setDescription('Add the repo hooks to the project');
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $fs = new Filesystem();
    // Install .git hooks into the new project.
    if (!empty($this->project_config['git']['hooks'])) {
      $output->writeln('<info>Creating Git hooks directory</info>');
      $fs->mkdir($this->project_directory . '/.git/hooks');

      // Copy the desirable hooks.
      foreach ($this->project_config['git']['hooks'] as $hook) {
        $output->writeln('<info>Copying Git hook ' . $hook . '</info>');
        $fs->symlink("{$this->project_directory}/scripts/git-hooks/{$hook}", "{$this->project_directory}/.git/hooks/{$hook}", TRUE);
      }
    }
  }
}
