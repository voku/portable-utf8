<?php

/**
 * Normalizer plugs Patchwork\PHP\Shim\Normalizer as a PHP implementation
 * of intl's Normalizer when the intl extension in not enabled.
 */
if (!class_exists('Normalizer')) {
  /**
   * Class Normalizer
   */
  class Normalizer extends Patchwork\PHP\Shim\Normalizer
  {
  }
}
