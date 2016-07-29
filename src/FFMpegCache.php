<?php

/**
 * @file
 * @todo add file description
 */

namespace Drupal\php_ffmpeg;

use \Doctrine\Common\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Class FFMpegCache
 *
 * Adapter between Doctrine cache needed by FFMPeg library and Drupal cache.
 */
class FFMpegCache implements Cache {

  /**
   * The cache backend that should be used.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Constructs a CacheCollector object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * @inheritdoc
   */
  public function fetch($id) {
    return $this->cache->get($id);
  }

  /**
   * @inheritdoc
   */
  public function contains($id) {
    return !!$this->cache->get($id);
  }

  /**
   * @inheritdoc
   */
  public function save($id, $data, $lifeTime = 0) {
    $this->cache->set($id, $data, time() + $lifeTime);
  }

  /**
   * @inheritdoc
   */
  public function delete($id) {
    $this->cache->delete($id);
  }

  /**
   * @inheritdoc
   */
  public function getStats() {
    return NULL;
  }

}
