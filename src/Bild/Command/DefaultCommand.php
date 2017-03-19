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
  public function initialize($options = ['set-project-dir' => FALSE,'no-gitignore' => FALSE, 'no-composer' => FALSE, 'no-docs' => FALSE, 'no-scripts' => FALSE, 'no-tests' => FALSE]) {
    $git = new GitWrapper();
    $fs = new Filesystem();
    $project_dir = $options['set-project-dir']?$options['set-project-dir']:getcwd();
    // Set up new project directory, fail if directory exists.
    if ($fs->exists($project_dir)) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    }
    $fs->mkdir($project_dir);

    // Load bild defaults directory.
    $defaults_dir = str_replace('src/Bild/Command', 'defaults', pathinfo(__FILE__, PATHINFO_DIRNAME));

    //TODO - Set gitignore, docs, tests, and composer settings in bild.yml before writing.

    // Copy bild configuration file.
    $fs->copy($defaults_dir . '/config/default.bild.yml', $project_dir . '/bild.yml');
    print "\nDefault bild.yml configuration has been created in " . $project_dir . ". Please customize and run the setup command.";

    // Copy default gitignore file.
    if (!$options['no-gitignore']) {
      $git->init($project_dir);
      $fs->copy($defaults_dir . '/config/default.gitignore', $project_dir . '/.gitignore');
      print "\nInitial git repo and .gitignore has been created in " . $project_dir . ".";
    }

    // Copy default composer file.
    if (!$options['no-composer']) {
      $fs->copy($defaults_dir . '/config/default.composer.json', $project_dir . '/bild.yml');
      print "\nDefault composer.json has been created in " . $project_dir . ". Please customize and run the setup command.";
    }

    // Copy default docs file.
    if (!$options['no-docs']) {
      $fs->mirror($defaults_dir . '/docs', $project_dir . '/docs');
      print "\nInitial docs has been created in " . $project_dir . ".";
    }

    // Stub out scripts directory.
    if (!$options['no-scripts']) {
      $fs->mkdir($project_dir . '/scripts');
      print "\nScripts directory created in " . $project_dir . ".";
    }

    // Stub out tests directory.
    if (!$options['no-tests']) {
      $fs->mkdir($project_dir . '/test');
      print "\nTests directory created in " . $project_dir . ".";
    }
  }

  /**
   * This performs initial setup of a project.
   *
   * @command project:setup
   */
  public function setup($options = ['set-project-dir' => FALSE, 'drupal-version' => '8.4.x', 'no-composer' => FALSE, 'drupal-docroot' => 'docroot']) {
    $git = new GitWrapper();
    $fs = new Filesystem();
    $project_dir = $options['set-project-dir']?$options['set-project-dir']:getcwd();
    // Run composer install.
    if ($options['no-composer']) {
      $process = new Process("composer install");
      $process->setTimeout(3600);
      $process->setWorkingDirectory($project_dir);
      $process->run();
    }

    // Make a new Drupal docroot.
    if ($fs->exists($project_dir . '/' . $options['drupal-docroot'])) {
      throw new \RuntimeException("The project directory already exists, you cannot create a new project here");
    } else {
      $git->cloneRepository('https://git.drupal.org/project/drupal.git', $project_dir  . '/' . $options['drupal-docroot'], [
        'branch' => $options['drupal-version'],
      ]);

      // Remove Drupal git repository.
      $fs->remove($project_dir . '/' . $options['drupal-docroot'] . '/.git');
    }
  }
}