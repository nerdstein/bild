<?php

namespace Bild\Hook;

use Consolidation\AnnotatedCommand\Hooks\InitializeHookInterface;
use Consolidation\AnnotatedCommand\AnnotationData;
use Symfony\Component\Console\Input\InputInterface;

class ConfigurationHook implements InitializeHookInterface {

  /**
   * @inheritdoc
   */
  public function initialize(InputInterface $input, AnnotationData $data) {
    $input->setOption('how-much', '42');
  }
}