<?php

/**
 * @file
 */

namespace Bild\Console\Command\Testing;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 *
 */
class ValidatePHPCS extends BaseCommand {

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate-phpcs')
      ->setDescription('Validate PHPCS on codebase')
      ->addArgument(
        'file-path',
        InputArgument::OPTIONAL,
        'Which file path(s) do you wish to check? If not specified, this will run default options in your config.yml'
      );
  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln("<fg=white;options=bold;bg=red>Running the PHPCS Codesniffer.</fg=white;options=bold;bg=red>");
    $file_path = $input->getArgument('file-path');

    if ($file_path) {
      $files = explode(',', $file_path);
    }
    else {
      $files = $this->getValidationFiles();
    }
    $ignored = $this->getIgnoredFiles();
    $ignored_files = implode(',',$ignored);

    //$output->writeln("<info>Ignoring $ignored_files.</info>");

    if (!$files) {
      $output->writeln("<info>No files to check.</info>");
      return 0;
    }

    $output->writeln("<info>File(s) to be processed/validated:...</info>");

    $sniffer_error_count = 0;
    foreach ($files as $key => $name) {
      $output->writeln("<info>$name</info>");
      if (!$this->phpcs($name, $ignored_files)) {
        $sniffer_error_count++;
      }
    }

    if ($sniffer_error_count > 0) {
      throw new \RuntimeException("$sniffer_error_count error(s) detected!");
    }
  }

  /**
   *
   */
  public function phpcs($filename, $ignore_files = null) {

    $cmd = "{$this->bin}/phpcs --runtime-set ignore_warnings_on_exit 1 --standard=Drupal";

    if (!empty($ignore_files)) {
      $cmd .= " --ignore=$ignore_files";
    }

    $cmd .= " $filename";

    $process = new Process($cmd);
    $process->setTimeout(3600);

    $process->run(
      function ($type, $buffer) {
        print $buffer;
      }
    );

    if (!$process->isSuccessful()) {
      return FALSE;
    }
    return TRUE;
  }

}
