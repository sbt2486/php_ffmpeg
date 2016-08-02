<?php
namespace Drupal\php_ffmpeg\Tests;

use \Drupal\simpletest\WebTestBase;

/**
 * Test the API and basic function of the PHPFFMpeg module.
 *
 * @group php_ffmpeg
 */
class PHPFFMpegTestCase extends WebTestBase {

  protected $profile = 'standard';

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['php_ffmpeg'];

  /**
   * Boring tests of the administration page.
   */
  public function testAdminPage() {
    $account = $this->drupalCreateUser([], NULL, TRUE);

    $php_ffmpeg_ffmpeg_binary = $this->randomString();
    $php_ffmpeg_ffprobe_binary = $this->randomString();
    $php_ffmpeg_timeout = mt_rand(1, 42);
    $php_ffmpeg_threads = mt_rand(1, 42);

    $this->config('php_ffmpeg.settings')
      ->set('php_ffmpeg_ffmpeg_binary', $php_ffmpeg_ffmpeg_binary)
      ->set('php_ffmpeg_ffprobe_binary', $php_ffmpeg_ffprobe_binary)
      ->set('php_ffmpeg_timeout', $php_ffmpeg_timeout)
      ->set('php_ffmpeg_threads', $php_ffmpeg_threads)
      ->save();
    $settings = $this->config('php_ffmpeg.settings');

    $this->drupalLogin($account);
    $this->drupalGet('admin/config/development/php-ffmpeg');

    $this->assertFieldByName('php_ffmpeg_ffmpeg_binary', $php_ffmpeg_ffmpeg_binary, 'The PHP-FFMpeg settings page should provide a field for the ffmpeg binary path.');
    $this->assertFieldByName('php_ffmpeg_ffprobe_binary', $php_ffmpeg_ffprobe_binary, 'The PHP-FFMpeg settings page should provide a field for the ffprobe binary path.');
    $this->assertFieldByName('php_ffmpeg_timeout', $php_ffmpeg_timeout, 'The PHP-FFMpeg settings page should provide a field for the ffmpeg command timeout.');
    $this->assertFieldByName('php_ffmpeg_threads', $php_ffmpeg_threads, 'The PHP-FFMpeg settings page should provide a field for the number of threads to use for ffmpeg commands.');

    $php_ffmpeg_ffmpeg_binary = drupal_realpath($this->drupalGetTestFiles('binary')[0]->uri);
    $php_ffmpeg_ffprobe_binary = drupal_realpath($this->drupalGetTestFiles('binary')[1]->uri);
    $php_ffmpeg_timeout = mt_rand(1, 42);
    $php_ffmpeg_threads = mt_rand(1, 42);

    $this->drupalPostForm(NULL, [
      'php_ffmpeg_ffmpeg_binary' => $php_ffmpeg_ffmpeg_binary,
      'php_ffmpeg_ffprobe_binary' => $php_ffmpeg_ffprobe_binary,
      'php_ffmpeg_timeout' => $php_ffmpeg_timeout,
      'php_ffmpeg_threads' => $php_ffmpeg_threads,
    ], 'Save configuration');
    $settings = $this->config('php_ffmpeg.settings');

    $this->assertFieldByName('php_ffmpeg_ffmpeg_binary', $php_ffmpeg_ffmpeg_binary, 'Submitting he PHP-FFMpeg settings page should update the value of the field for the ffmpeg binary path.');
    $this->assertFieldByName('php_ffmpeg_ffprobe_binary', $php_ffmpeg_ffprobe_binary, 'Submitting he PHP-FFMpeg settings page should update the value of the field for the ffprobe binary path.');
    $this->assertFieldByName('php_ffmpeg_timeout', $php_ffmpeg_timeout, 'Submitting he PHP-FFMpeg settings page should update the value of the field for the ffmpeg command timeout.');
    $this->assertFieldByName('php_ffmpeg_threads', $php_ffmpeg_threads, 'Submitting he PHP-FFMpeg settings page should update the value of the field for the number of threads to use for ffmpeg commands.');

    $this->assertEqual($settings->get('php_ffmpeg_ffmpeg_binary'), $php_ffmpeg_ffmpeg_binary, 'Submitting he PHP-FFMpeg settings page should update the ffmpeg binary path.');
    $this->assertEqual($settings->get('php_ffmpeg_ffprobe_binary'), $php_ffmpeg_ffprobe_binary, 'Submitting he PHP-FFMpeg settings page should update the ffproe binary path.');
    $this->assertEqual($settings->get('php_ffmpeg_timeout'), $php_ffmpeg_timeout, 'Submitting he PHP-FFMpeg settings page should update the ffmpeg command timeout.');
    $this->assertEqual($settings->get('php_ffmpeg_threads'), $php_ffmpeg_threads, 'Submitting he PHP-FFMpeg settings page should update the number of threads to use for ffmpeg commands.');

    $invalidFilenames = [$this->randomMachineName(), $this->randomMachineName()];

    $this->drupalPostForm(NULL, [
      'php_ffmpeg_ffmpeg_binary' => $invalidFilenames[0],
      'php_ffmpeg_ffprobe_binary' => $invalidFilenames[1],
      'php_ffmpeg_timeout' => $this->randomString(),
      'php_ffmpeg_threads' => $this->randomString(),
    ], 'Save configuration');
    $settings = $this->config('php_ffmpeg.settings');

    $this->assertText("File not found: $invalidFilenames[0]", "Submission of the the PHP-FFMpeg settings page should validate the ffmpeg binary path is an existing file.");
    $this->assertText("File not found: $invalidFilenames[1]", "Submission of the the PHP-FFMpeg settings page should validate the ffprobe binary path is an existing file.");
    $this->assertText('The value of the Timeout field must be a positive integer.', "Submission of the the PHP-FFMpeg settings page should validate the ffmpeg command timeout is a positive integer.");
    $this->assertText('The value of the Threads field must be zero or a positive integer.', "Submission of the the PHP-FFMpeg settings page should validate the ffmpeg command threads number is a positive integer.");

    $this->assertEqual($settings->get('php_ffmpeg_ffmpeg_binary'), $php_ffmpeg_ffmpeg_binary, 'Submitting he PHP-FFMpeg settings page with invalid values should not update the ffmpeg binary path.');
    $this->assertEqual($settings->get('php_ffmpeg_ffprobe_binary'), $php_ffmpeg_ffprobe_binary, 'Submitting he PHP-FFMpeg settings page with invalid values should not update the ffprobe path.');
    $this->assertEqual($settings->get('php_ffmpeg_timeout'), $php_ffmpeg_timeout, 'Submitting he PHP-FFMpeg settings page with invalid values should not update the ffmpeg command time path.');
    $this->assertEqual($settings->get('php_ffmpeg_threads'), $php_ffmpeg_threads, 'Submitting he PHP-FFMpeg settings page with invalid values should not update the ffmpeg command threads number.');

  }

  public function testFactories() {
    chmod(drupal_realpath($this->drupalGetTestFiles('binary')[0]->uri), 0777);
    chmod(drupal_realpath($this->drupalGetTestFiles('binary')[1]->uri), 0777);
    $this->config('php_ffmpeg.settings')
      ->set('php_ffmpeg_ffmpeg_binary', drupal_realpath($this->drupalGetTestFiles('binary')[0]->uri))
      ->set('php_ffmpeg_ffprobe_binary', drupal_realpath($this->drupalGetTestFiles('binary')[1]->uri))
      ->save();

    /** @var \FFMpeg\FFMpeg $ffmpeg */
    $ffmpeg = \Drupal::service('php_ffmpeg');
    $this->assertTrue($ffmpeg instanceof \FFMpeg\FFMpeg, "\Drupal::service('php_ffmpeg') should return an instance of \FFMpeg\FFMpeg.");

    /** @var \FFMpeg\FFProbe $ffprobe */
    $ffprobe =  \Drupal::service('php_ffmpeg.factory')->getFFMpegProbe();
    $this->assertTrue($ffprobe instanceof \FFMpeg\FFProbe, "\Drupal::service('php_ffmpeg.factory')->getFFMpegProbe() should return an instance of \FFMpeg\FFProbe.");
  }

}
