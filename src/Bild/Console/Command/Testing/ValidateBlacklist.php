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
class ValidateBlacklist extends BaseCommand {

  private $checks;

  /**
   *
   */
  public function __construct($name = NULL) {

    // Build the list of PHP blacklisted functions.
    $this->checks[] = '/var_dump\(/';
    $this->checks[] = '/print_r\(/';
    $this->checks[] = '/die\(/';

    // Blacklist Drupal's built-in debugging function.
    $this->checks[] = '/debug\(/';

    // Blacklist Devel's debugging functions.
    $this->checks[] = '/dpm\(/';
    $this->checks[] = '/krumo\(/';
    $this->checks[] = '/dpr\(/';
    $this->checks[] = '/dsm\(/';
    $this->checks[] = '/dd\(/';
    $this->checks[] = '/ddebug_backtrace\(/';
    $this->checks[] = '/dpq\(/';
    $this->checks[] = '/dprint_r\(/';
    $this->checks[] = '/drupal_debug\(/';
    $this->checks[] = '/dsm\(/';
    $this->checks[] = '/dvm\(/';
    $this->checks[] = '/dvr\(/';
    $this->checks[] = '/kpr\(/';
    $this->checks[] = '/kprint_r\(/';
    $this->checks[] = '/kdevel_print_object\(/';
    $this->checks[] = '/kdevel_print_object\(/';

    // Blacklist code conflicts resulting from Git merge.
    $this->checks[] = '/<<<<<<</';
    $this->checks[] = '/>>>>>>>/';

    // Blacklist Javascript debugging functions.
    $this->checks[] = '/console.log\(/';
    $this->checks[] = '/alert\(/';

    parent::__construct($name);
  }

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('testing:validate-blacklist')
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
    $output->writeln("<fg=white;options=bold;bg=red>Check for debugging functions.</fg=white;options=bold;bg=red>");
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

    $output->writeln("<info>Blacklisting validation for:</info>");

    foreach ($files as $key => $name) {

      $output->writeln("<info>$name</info>");
    }

    $blacklist_errors = array();

    $blacklist_errors_count = 0;
    $ignored_files = $this->getIgnoredFiles();

    foreach ($files as $key => $name) {
      $ignored = FALSE;
      foreach ($ignored_files as $ignore) {
        if (fnmatch($ignore, $this->project_directory . '/' . $name)) {
          $ignored = TRUE;
        }
      }
      if ($ignored) {
        $output->writeln("Blacklist ignored $name");
      } else {
        $blacklist_errors = $this->phpblacklist($name);
        $blacklist_errors_count += count($blacklist_errors);

        foreach ($blacklist_errors as $error) {
          $output->writeln("<info>Black list error $error</info>");
        }
      }
    }

    if ($blacklist_errors_count > 0) {
      $output->writeln("$blacklist_errors_count error(s) detected!");
      throw new \RuntimeException("$blacklist_errors_count error(s) detected!");
    }
    else {
      $output->writeln("<info>No backlisted function(s) detected.</info>");
    }
  }

  /**
   *
   */
  public function phpblacklist($filename) {
    $fh = fopen($filename, 'r') or die('Cannot open file.');
    $line_num = 1;

    $blacklist = [];

    while (!feof($fh)) {
      $line = fgets($fh, 4096);

      foreach ($this->checks as $check) {
        if (preg_match("$check", $line)) {
          $blacklist[] = "Line $line_num: $line";
        }
      }
      $line_num++;
    }
    fclose($fh);

    return $blacklist;
  }

}
