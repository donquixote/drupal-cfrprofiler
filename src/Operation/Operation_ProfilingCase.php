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
    $dts = [];
    for ($i = $this->nRepetitions; $i > 0; --$i) {
      $this->profilingCase->reset();
      $t0 = microtime(TRUE);
      $this->profilingCase->run();
      $t1 = microtime(TRUE);
      $dts[] = ($t1 - $t0) * 1000;
    }

    sort($dts);
    $fastest = $dts[0];
    $slowest = end($dts);
    drupal_set_message("Duration: $fastest - $slowest ms.");
  }
}
