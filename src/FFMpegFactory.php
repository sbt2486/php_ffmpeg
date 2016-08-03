<?php

namespace Drupal\php_ffmpeg;

/**
 * Factory class that provides a wrapper for the FFMpeg PHP extension.
 */
class FFMpegFactory {

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
        \Drupal::logger('php_ffmpeg'),
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
        \Drupal::logger('php_ffmpeg'),
        \Drupal::service('php_ffmpeg.cache')
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
