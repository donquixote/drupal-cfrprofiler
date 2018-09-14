<?php

namespace Drupal\cfrprofiler\ProfilingCase;

interface ProfilingCaseInterface {

  /**
   * Clears static caches etc.
   *
   * This method does not count on profiling time.
   */
  public function reset();

  /**
   * No return value.
   *
   * @throws \Exception
   */
  public function run();

}
