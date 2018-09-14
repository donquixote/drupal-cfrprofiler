<?php

namespace Drupal\cfrprofiler\Operation;

use Drupal\cfrop\Operation\OperationInterface;
use Drupal\cfrprofiler\ProfilingCase\ProfilingCaseInterface;

class Operation_ProfilingCase implements OperationInterface {

  /**
   * @var \Drupal\cfrprofiler\ProfilingCase\ProfilingCaseInterface
   */
  private $profilingCase;

  /**
   * @var int
   */
  private $nRepetitions;

  /**
   * @CfrPlugin("profilingCase", "Profiling case")
   *
   * @param \Drupal\cfrprofiler\ProfilingCase\ProfilingCaseInterface $profilingCase
   *
   * @return self
   */
  public static function createWith20(ProfilingCaseInterface $profilingCase) {
    return new self($profilingCase, 20);
  }

  /**
   * @param \Drupal\cfrprofiler\ProfilingCase\ProfilingCaseInterface $profilingCase
   * @param int $nRepetitions
   */
  public function __construct(ProfilingCaseInterface $profilingCase, $nRepetitions) {
    $this->profilingCase = $profilingCase;
    $this->nRepetitions = $nRepetitions;
  }

  /**
   * Runs the operation. Returns nothing.
   */
  public function execute() {

    foreach (['xhprof', 'xdebug'] as $extension_name) {
      if (\extension_loaded($extension_name)) {
        drupal_set_message(
          t(
            'The PHP extension "@extension" is enabled, and might contaminate your profiling results.',
            [
              '@extension' => $extension_name,
            ]),
          'warning');
      }
    }

    try {
      $this->doExecute();
    }
    catch (\Exception $e) {
      drupal_set_message("Exception in profiling case.");
      return;
    }
  }

  /**
   * @throws \Exception
   */
  private function doExecute() {
    $dts = $this->repeat($this->nRepetitions);
    sort($dts);
    $msFastest = $dts[0];
    $nExtra = floor(200 / $msFastest);
    $dtsExtra = $this->repeat($this->nRepetitions);
    drupal_set_message("Repetitions: $this->nRepetitions + $nExtra.");
    $dts = array_merge($dts, $dtsExtra);
    sort($dts);
    $msFastest = $dts[0];
    $msSlowest = end($dts);
    drupal_set_message("Duration: $msFastest - $msSlowest ms.");
  }

  /**
   * @param int $n
   *
   * @return float[]
   * @throws \Exception
   */
  private function repeat($n) {

    $dts = [];
    for ($i = $n; $i > 0; --$i) {
      $this->profilingCase->reset();
      $t0 = microtime(TRUE);
      $this->profilingCase->run();
      $t1 = microtime(TRUE);
      $dts[] = ($t1 - $t0) * 1000;
    }

    return $dts;
  }
}
