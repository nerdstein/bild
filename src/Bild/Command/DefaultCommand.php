<?php

namespace Bild\Command;

use GitWrapper\GitWrapper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DefaultCommand {

  /**
   * This initializes the bild configuration and basic project options.
   *
   * @command project:initialize
   */
  public function initialize($arguments = ['project-dir' => __DIR__], $options = ['gitignore' => TRUE, 'composer' => TRUE, 'docs' => TRUE, 'scripts' => TRUE, 'tests' => TRUE]) {
    $fs = new Filesystem();
    // Set up new project directory, fail if directory exists.
    if ($fs->exists($arguments['project-dir'])) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    }
    $fs->mkdir($arguments['project-dir']);

    // Load bild defaults directory.
    $defaults_dir = str_replace('src/Bild/Command', 'defaults', pathinfo(__FILE__, PATHINFO_DIRNAME));

    //TODO - Set gitignore, docs, tests, and composer settings in bild.yml before writing.

    // Copy bild configuration file.
    $fs->copy($defaults_dir . '/config/default.bild.yml', $arguments['project-dir'] . '/bild.yml');
    print "\nDefault bild.yml configuration has been created in " . $arguments['project-dir'] . ". Please customize and run the setup command.";

    // Copy default gitignore file.
    if ($options['gitignore']) {
      $fs->copy($defaults_dir . '/config/default.gitignore', $arguments['project-dir'] . '/.gitignore');
      print "\nInitial .gitignore has been created in " . $arguments['project-dir'] . ".";
    }

    // Copy default composer file.
    if ($options['composer']) {
      $fs->copy($defaults_dir . '/config/default.composer.json', $arguments['project-dir'] . '/bild.yml');
      print "\nDefault composer.json has been created in " . $arguments['project-dir'] . ". Please customize and run the setup command.";
    }

    // Copy default docs file.
    if ($options['docs']) {
      $fs->mirror($defaults_dir . '/docs', $arguments['project-dir'] . '/docs');
      print "\nInitial docs has been created in " . $arguments['project-dir'] . ".";
    }

    // Stub out scripts directory.
    if ($options['scripts']) {
      $fs->mkdir($arguments['project-dir'] . '/scripts');
      print "\nScripts directory created in " . $arguments['project-dir'] . ".";
    }

    // Stub out tests directory.
    if ($options['tests']) {
      $fs->mkdir($arguments['project-dir'] . '/test');
      print "\nTests directory created in " . $arguments['project-dir'] . ".";
    }
  }

  /**
   * This performs initial setup of a project.
   *
   * @command project:setup
   */
  public function setup($arguments = ['project-dir' => __DIR__], $options = []) {
    $fs = new Filesystem();
    // Run composer install.
    if ($options['composer']) {
      $process = new Process("composer install");
      $process->setTimeout(3600);
      $process->setWorkingDirectory($arguments['project-dir']);
      $process->run();
    }

    // Make a docroot.
    if ($fs->exists($arguments['project-dir'] . '/docroot')) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    } else {
      $git = new GitWrapper();
      $git->cloneRepository('https://git.drupal.org/project/drupal.git', $arguments['project-dir'] . '/docroot', [
        'branch' => $options['drupal_version'],
      ]);

      // Remove Drupal git repository.
      $fs->remove('docroot/.git');
    }
  }
}