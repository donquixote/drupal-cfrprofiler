<?php

namespace Drupal\cfrprofiler\Operation;

use Drupal\cfrapi\Configurator\Configurator_IntegerInRange;
use Drupal\cfrop\Operation\OperationInterface;
use Drupal\cfrprofiler\ProfilingCase\ProfilingCaseInterface;
use Drupal\cfrreflection\Configurator\Configurator_CallbackConfigurable;

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
   * @return \Drupal\cfrapi\Configurator\ConfiguratorInterface
   */
  public static function plugin() {
    return Configurator_CallbackConfigurable::createFromClassName(
      self::class,
      [
        cfrplugin()->interfaceGetConfigurator(ProfilingCaseInterface::class),
        new Configurator_IntegerInRange(1, NULL),
      ],
      [
        t('Profiling case'),
        t('Number of repetitions'),
      ]);
  }

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
    $t0 = microtime(TRUE);
    for ($i = $this->nRepetitions; $i > 0; --$i) {
      $this->profilingCase->run();
      $t0 += ($dts[] = microtime(TRUE) - $t0);
    }
    sort($dts);
    foreach ($dts as &$dt) {
      $dt *= 1000;
    }
    $fastest = $dts[0];
    $slowest = end($dts);
    drupal_set_message("Duration: $fastest - $slowest ms.");
  }
}
