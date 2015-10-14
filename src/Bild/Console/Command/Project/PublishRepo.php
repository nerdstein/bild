<?php

/**
 * @file
 * Deploys project code and build artifacts to a repo.
 *
 * This is triggered in travis if a release is tagged in github.
 *
 * If a release is tagged with a -dev or -rc then the 'integration-branch' will
 * be tagged and pushed up to the target repo. Otherwise the 'prod-branch' will
 * be tagged and pushed up to the target repo.
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class PublishRepo extends BaseCommand {


  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:publish')
      ->setDescription('Deploy codebase and build artifacts.')
      ->addArgument(
        'tag',
        InputArgument::REQUIRED,
        'Which tag to sync.'
      )
      ->addArgument(
        'target-repo',
        InputArgument::REQUIRED,
        'Which repo is the sync?'
      )
      ->addArgument(
        'target-branch',
        InputArgument::REQUIRED,
        'Which branch on the target?'
      )
      ->addOption(
        'process-tag',
        'pt',
        InputOption::VALUE_NONE,
        'Process the tag to determine the branch.'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    
    $tag = $input->getArgument('tag');

    $target_repo = $input->getArgument('target-repo');
    $target_branch = $input->getArgument('target-branch');

    if (empty($project_config['git']['remotes'][$target_repo])) {
      $output->writeln('<error>Target repo not found in config.yml for project</error>');
      throw new \RuntimeException;
    }
    $target_repo_name = $this->project_config['git']['remotes'][$target_repo];

    $output->writeln("<info>Syncing to $target_repo_name...</info>");

    // Set owner:group for all project files.
    $cmd = "sudo chown -R travis:travis {$this->project_directory}";
    $this->executeProcess($cmd);

    // Tag a release.
    if (!empty($tag)) {

      // If this is travis, determine the branch to push changes to by the
      // format of the tag.
      if ($input->getOption('process-tag')) {
        if (stristr($tag, '-dev') || stristr($tag, '-rc')) {
          $target_branch = $this->project_config['deployment']['integration-branch'];
        }
        else {
          $target_branch = $this->project_config['deployment']['prod-branch'];
        }
      }
      $message = "Repo:sync deploy.";

      // Deploy code to target.
      // Remove gitignore on everything under docroot.
      $this->executeProcess('echo "\n\!docroot" >> .gitignore', TRUE, $this->project_directory);

      // Allow compiled css/js to be included in git.
      foreach ($this->project_config['frontend']['compiled_resources'] as $resource) {
        $cmd = 'echo "\n!' . $resource . '" >> .gitignore';
        $this->executeProcess($cmd, TRUE, $this->project_directory);
      }

      $this->executeProcess("git clone -b $target_branch --single-branch $target_repo_name project2", TRUE, '/tmp');
      $this->executeProcess('cp -rf project2/.git ' . $this->project_directory, TRUE, '/tmp');
      $this->executeProcess('rm -rf project2', TRUE, '/tmp');

      // Remove travis site config.
      $this->executeProcess("sudo rm -rf docroot/sites/travisci.local", TRUE, $this->project_directory);
      $output->writeln('<info>Add remotes</info>');

      $command = $this->getApplication()->find('repo:add-remotes');

      $command_arguments = array(
        'command' => 'repo:add-remotes',
        '--sudo' => true
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);

      // Add all files and commit.
      $this->executeProcess("git add --all", FALSE, $this->project_directory);
      $ret = $this->executeProcess("git commit -m 'Build $tag Artifacts.'", TRUE, $this->project_directory);

      // Set owner:group for all project files.
      $cmd = "sudo chown -R travis:travis $this->project_directory";
      $this->executeProcess($cmd);

      $output->writeln('<info>Sync Repositories.</info>');
      $command = $this->getApplication()->find('project:sync');

      $command_arguments = array(
        'command' => 'project:sync',
        'target-repo' => $target_repo,
        'target-branch' => $target_branch
      );

      $command_input = new ArrayInput($command_arguments);
      $returnCode = $command->run($command_input, $output);

      // Move tag to latest commit.
      $this->executeProcess("git tag -a $tag -m '$message'", TRUE, $this->project_directory);

      // Push tag to target.
      $output->writeln("<info>Push $tag to $target_repo</info>");
      $ret = $this->executeProcess("git push $target_repo $tag", TRUE, $this->project_directory);

    }
  }

}
