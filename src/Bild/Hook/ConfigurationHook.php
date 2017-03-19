<?php

namespace Bild\Hook;

use Consolidation\AnnotatedCommand\Hooks\InitializeHookInterface;
use Consolidation\AnnotatedCommand\AnnotationData;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Parser;

//use Symfony\Component\Config;

class ConfigurationHook implements InitializeHookInterface {

  /**
   * @inheritdoc
   */
  public function initialize(InputInterface $input, AnnotationData $data) {
    //TODO - Explore Symfony Config.
    /*$configDirectories = array(__DIR__);
    $locator = new Config\FileLocator($configDirectories);
    $config = $locator->locate('bild.yml', NULL, TRUE);*/

    //TODO - Explore "state" of application to set config not as option.

    // Load config from current runtime directory.
    $project_config = getcwd().'/bild.yml';
    if (file_exists($project_config)) {
      //TODO - Consider config validation.

      $parser = new Parser();
      $project_config = $parser->parse(file_get_contents($project_config));

      foreach ($project_config as $item => $value) {
        // We need to check if the command is looking for this config.
        if ($input->hasOption($item)) {
          // If so, set the value.
          $input->setOption($item, $value);
        }
      }

    }
  }
}