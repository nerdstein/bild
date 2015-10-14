<?php

/**
 * @file
 * RunPHPCBF command. Executes automated code beautification.
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class RunPHPCBF.
 */
class RunPHPCBF extends BaseCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:run-phpcbf')
      ->setDescription('Run PHP Code Beautifier on codebase')
      ->addArgument(
        'file-path',
        InputArgument::OPTIONAL,
        'Which file path(s) do you wish to check? ' .
        'If not specified, this will run default options in your config.yml'
      )
      ->addArgument(
        'git-add',
        InputArgument::OPTIONAL,
        'T/F, do you wish to add these files to Git after running the cleanup?'
      );
  }


  /**
   * @{inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('<fg=white;options=bold;bg=red>Running the ' .
      'PHP Code Beautifier.</fg=white;options=bold;bg=red>');
    $file_path = $input->getArgument('file-path');
    $git_add = $input->getArgument('git-add');

    if ($file_path) {
      $files = explode(',', $file_path);
    }
    else {
      $files = $this->getValidationFiles();
    }

    if (!$files) {
      $output->writeln("<info>No files to check.</info>");
      return 0;
    }

    $output->writeln("<info>File(s) running PHP Code Beautify:...</info>");

    foreach ($files as $key => $name) {
      $output->writeln("<info>$name</info>");
    }

    $sniffer_error_count = 0;

    foreach ($files as $key => $name) {

      if (!$this->phpcbf("$name", $git_add)) {
        $sniffer_error_count++;
      }
    }

    if ($sniffer_error_count > 0) {
      $output->writeln("<info>$sniffer_error_count file(s) cleaned up</info>");
    }
  }

  /**
   * Executes PHPCBF on file.
   *
   * @param string $filename
   *   The full path and filename.
   * @param bool $gitadd
   *   T/F to add the file to the git repo.
   */
  public function phpcbf($filename, $gitadd = FALSE) {
    $process = new Process("{$this->bin}/phpcbf $filename --standard=Drupal");
    $process->setTimeout(3600);

    $process->run(
      function ($type, $buffer) {
        print $buffer;
      }
    );

    if (!$process->isSuccessful()) {
      return FALSE;
    }

    if (!$gitadd) {
      $cmd = 'git add $filename';
      $this->executeProcess($cmd, FALSE, $this->project_directory);
    }

    return TRUE;
  }

}
