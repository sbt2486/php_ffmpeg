<?php

namespace Drupal\php_ffmpeg;

use Drupal\Core\Logger\LoggerChannelInterface;
use Doctrine\Common\Cache\Cache;

/**
 * Factory class that provides a wrapper for the FFMpeg PHP extension.
 */
class PHPFFMpegFactory {

  /**
   * The cache backend that should be passed to the FFMpeg extension.
   *
   * @var \Doctrine\Common\Cache\Cache
   */
  protected $cache;

  /**
   * Logger channel that logs execution within FFMpeg extension to watchdog.
   *
   * @var Drupal\Core\Logger\LoggerChannelInterface
   *   The registered logger for this channel.
   */
  protected $logger;

  /**
   * Constructs a CacheCollector object.
   *
   * @param \Doctrine\Common\Cache\Cache $cache
   *   The cache backend.
   * @param Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Prefix used for appending to cached item identifiers.
   */
  public function __construct(Cache $cache, LoggerChannelInterface $logger) {
    $this->cache = $cache;
    $this->logger = $logger;
  }

  /**
   * Factory function for the FFMpeg object.
   *
   * @return \FFMpeg\FFMpeg
   */
  public function getFFMpeg() {
    static $ffmpeg = NULL;
    if (!$ffmpeg) {
      $ffmpeg = \FFMpeg\FFMpeg::create(
        $this->getFFMpegConfig(),
        $this->logger,
        $this->getFFMpegProbe()
      );
    }
    return $ffmpeg;
  }

  /**
   * Factory function for the FFProbe object.
   *
   * @return \FFMpeg\FFProbe
   */
  public function getFFMpegProbe() {
    static $ffprobe = NULL;
    if (!$ffprobe) {
      $ffprobe = \FFMpeg\FFProbe::create(
        $this->getFFMpegConfig(),
        $this->logger,
        $this->cache
      );
    }
    return $ffprobe;
  }

  /**
   * Provides configuration settings passed to FFMpeg classes' create methods.
   *
   * @return array
   *   Options based on settings as required by to \FFMpeg\FFMpeg::create().
   */
  protected function getFFMpegConfig() {
    return array_filter(array(
      'ffmpeg.binaries'  => \Drupal::config('php_ffmpeg.settings')->get('ffmpeg_binary'),
      'ffprobe.binaries' => \Drupal::config('php_ffmpeg.settings')->get('ffprobe_binary'),
      'timeout'          => \Drupal::config('php_ffmpeg.settings')->get('execution_timeout'),
      'ffmpeg.threads'   => \Drupal::config('php_ffmpeg.settings')->get('threads_amount'),
    ));
  }

}
