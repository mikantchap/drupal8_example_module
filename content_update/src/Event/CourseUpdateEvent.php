<?php
namespace Drupal\content_update\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @see Drupal\content_update\Event\CourseUpdateEvent
 *
 * @ingroup content_update
 */
class CourseUpdateEvent extends Event {

  /**
   * Incident type.
   *
   * @var boolean
   */
  protected $abortFlag;

  /**
   * Detailed incident report.
   *
   * @var array
   */
  protected $updateLog;

  /**
   * Constructs an incident report event object.
   *
   * @param string $type
   *   The incident report type.
   * @param string $report
   *   A detailed description of the incident provided by the reporter.
   */
  public function __construct($updateLog, $abortFlag) {
    $this->updateLog = $updateLog;
    $this->abortFlag = $abortFlag;
  }

  /**
   * Get the log of updates.
   *
   * @return array
   *   
   */
  public function getLog() {
    return $this->updateLog;
  }

  /**
   * Whether the update was aborted due to bad external data.
   *
   * @return boolean
   *   
   */
  public function getAbortFlag() {
    return $this->abortFlag;
  }
}
