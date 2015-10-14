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
class ValidatePHPSyntax extends BaseCommand {

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate-phpsyntax')
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
    $output->writeln("<fg=white;options=bold;bg=red>Syntax checking using PHP Linter.</fg=white;options=bold;bg=red>");
    $file_path = $input->getArgument('file-path');

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

    $output->writeln("<info>File(s) to be processed/validated:...</info>");

    foreach ($files as $key => $name) {
      $output->writeln("<info>$name</info>");
    }

    $syntax_error_count = 0;

    foreach ($files as $key => $name) {
      if (!$this->phpsyntax("$name", $this->project_config['validation']['extensions'])) {
        $syntax_error_count++;
      }
    }

    if ($syntax_error_count > 0) {
      throw new \RuntimeException("$syntax_error_count error(s) detected!");
    }
  }

  /**
   *
   */
  public function phpsyntax($filename, $extensions) {
    // Define file extensions used for linting.

    $extensions = implode(',', $extensions);

    $process = new Process("{$this->bin}/phplint $filename --extensions=$extensions");
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
