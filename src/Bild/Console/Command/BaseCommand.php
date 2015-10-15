<?php

/**
 * @file
 */

namespace Bild\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Parser;

/**
 *
 */
class BaseCommand extends Command {


  public $project_directory;
  public $project_config;
  public $bin;

  /**
   * @{inheritdoc}
   */
  public function __construct($name = NULL) {
    parent::__construct($name);
  }

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {
    $this
      ->addOption(
        'project-dir',
        'd',
        InputOption::VALUE_REQUIRED,
        'Which directory does your project reside?'
      );
  }

  /**
   * @{inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    $this->project_directory = $input->getOption('project-dir');
    if (!$this->project_directory) {
      //$this->project_directory = dirname(__FILE__);
      //$this->project_directory = dirname(getcwd());
      $this->project_directory = exec('pwd');
    }
    $this->setProjectConfig();

    $this->bin = str_replace('src/Bild/Console/Command', 'vendor/bin', dirname(__FILE__));
    $output->writeln('Bin directory => ' . $this->bin);
  }

  /**
   * Executes a command process directly.
   *
   * @param string $command
   *   The command to run.
   *
   * @param string $print
   *   The command to run.
   *
   * @return string
   *   The command output.
   */
  public static function executeProcess($command, $print = TRUE, $target = '') {
    $process = new Process($command);
    $process->setTimeout(3600);

    if (!empty($target)) {
      $process->setWorkingDirectory($target);
    }

    if ($print) {
      $process->run(
        function ($type, $buffer) {
          print $buffer;
        }
      );
    } else {
      $process->run();
    }

    if (!$process->isSuccessful()) {
      throw new \RuntimeException($process->getErrorOutput());
    }

    return $process->getOutput();
  }

  /**
   * Returns a parsed YAML configuration array.
   *
   * @param string $project_directory
   *
   * @return array
   *   The YAML config array.
   */
  public function setProjectConfig() {
    $project_config = $this->project_directory.'/bild.yml';

    // Load the example configuration file included with Drupal VM.
    $parser = new Parser();
    $this->project_config = $parser->parse(file_get_contents($project_config));
  }

  /**
   * Verifies files are in the defined validation directories.
   *
   * @param string $project_directory
   * @param string $file
   *
   * @return array
   *   The YAML config array.
   */
  public function checkValidationFile($file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    foreach ($this->project_config['validation']['directories'] as $dir) {
      if (substr($file, 0, strlen($dir)) === $dir
        and in_array($ext, $this->project_config['validation']['extensions'])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns validation directories.
   *
   * @param string $project_directory
   * @param string $file
   *
   * @return array
   *   The YAML config array.
   */
  public function getValidationFiles() {
    // Define project directories to detect.
    $files = array();
    if (isset($this->project_config['validation']) &&
      isset($this->project_config['validation']['directories'])
    ) {
      foreach ($this->project_config['validation']['directories'] as $dir) {
        $files[] = $this->project_directory . '/' . $dir;
      }
    }
    return $files;
  }

  /**
   * Returns ignored whitelist.
   *
   * @param string $project_directory
   * @param string $file
   *
   * @return array
   *   The YAML config array.
   */
  public function getIgnoredFiles() {
    // Define project directories to detect.
    $files = [];
    if (isset($this->project_config['validation']) &&
      isset($this->project_config['validation']['ignore'])
    ) {

      foreach ($this->project_config['validation']['ignore'] as $dir) {
        $files[] = $this->project_directory . '/' . $dir;
      }
    }
    return $files;
  }

}
