<?php

namespace Drupal\content_update\Event;

/**
 * Defines events for the content_update module.
 *
 * @see \Drupal\content_update\Event
 */
final class ContentUpdateEvents {

  /**
   * Name of the event fired when course update occurrs.
   * @Event
   *
   * @see \Drupal\content_update\Event\CourseUpdateEvent
   *
   * @var string
   */
  const COURSES_UPDATED = 'content_update.courses_updated';

}
