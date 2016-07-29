<?php

/**
 * @file
 * @todo add file description
 */

use \Doctrine\Common\Cache\Cache;

/**
 * Class FFMpegCache
 *
 * Adapter between Doctrine cache needed by FFMPeg library and Drupal cache.
 */
class FFMpegCache implements Cache {

  /**
   * @inheritdoc
   */
  public function fetch($id) {
    return \Drupal::cache()->get($id);
  }

  /**
   * @inheritdoc
   */
  public function contains($id) {
    return !!\Drupal::cache()->get($id);
  }

  /**
   * @inheritdoc
   */
  public function save($id, $data, $lifeTime = 0) {
    \Drupal::cache()->set($id, $data, time() + $lifeTime);
  }

  /**
   * @inheritdoc
   */
  public function delete($id) {
    \Drupal::cache()->delete($id);
  }

  /**
   * @inheritdoc
   */
  public function getStats() {
    return NULL;
  }

}
