<?php
/**
 * @file
 * Contains service for the FFMPeg API integration.
 */

/**
 * Factory class that provides a wrapper for the FFMpeg php extension.
 */
class FFMpegFactory {

  /**
   * Factory function for the FFMpeg object.
   *
   * @return \FFMpeg\FFMpeg
   */
  public function getFFMPeg() {
    static $ffmpeg = NULL;
    if (!$ffmpeg) {
      $ffmpeg = \FFMpeg\FFMpeg::create(
        $this->getFFMpegConfig(),
        \Drupal::logger('php_ffmpeg'),
        $this->phpFFMpegProbe()
      );
    }
    return $ffmpeg;
  }

  /**
   * Factory function for the FFProbe object.
   *
   * @return \FFMpeg\FFProbe
   */
  public function phpFFMpegProbe() {
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
   */
  protected function getFFMpegConfig() {
    return array_filter(array(
      'ffmpeg.binaries'  => \Drupal::config('php_ffmpeg.settings')->get('php_ffmpeg_ffmpeg_binary'),
      'ffprobe.binaries' => \Drupal::config('php_ffmpeg.settings')->get('php_ffmpeg_ffprobe_binary'),
      'timeout'          => \Drupal::config('php_ffmpeg.settings')->get('php_ffmpeg_timeout'),
      'ffmpeg.threads'   => \Drupal::config('php_ffmpeg.settings')->get('php_ffmpeg_threads'),
    ));
  }

}
