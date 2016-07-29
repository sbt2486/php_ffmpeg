<?php
namespace Drupal\php_ffmpeg\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the PHPFFMpeg Drupal watchdog to PSR-4 logger adapter.
 *
 * @group php_ffmpeg
 */
class PHPFFMpegLoggerTestCase extends WebTestBase {

  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => 'PHPFFMpeg Logger wrapper',
      'description' => 'The the PHPFFMpeg Drupal watchdog to PSR-4 logger adapter.',
      'group' => 'PHPFFMpeg',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp('php_ffmpeg');
  }

  public function testLog() {
    $type = $this->randomString();
    $logger = new PHPFFMpegLogger($type);

    $levels = [
      'emergency',
      'alert',
      'critical',
      'error',
      'warning',
      'notice',
      'info',
      'debug',
    ];

    $account = $this->drupalCreateUser(array_keys(system_permission()));
    $this->drupalLogin($account);
    $this->drupalPost('admin/reports/dblog', [], 'Clear log messages');

    foreach ($levels as $key => $level) {
      $logger->{$level}('{level} log message', ['level' => $level]);
      $this->drupalGet('admin/reports/dblog');
      $this->clickLink("$level log message");
      $this->assertRaw("<th>Type</th><td>$type</td>", "PHPFFMpegLogger::$level() should log the message using the type passed as argument to the constructor.");
      $this->assertRaw("<th>Message</th><td>$level log message</td>", "PHPFFMpegLogger::$level() should log the message with context substitution.");
      $this->assertRaw("<th>Severity</th><td>$level</td>", "PHPFFMpegLogger::$level() should log the message using the $level severity ");
    }

    $arbitrary_level = $this->randomName();
    $logger->log($arbitrary_level, '{level} log message', [
      'level' => $arbitrary_level
      ]);
    $this->drupalGet('admin/reports/dblog');
    $this->clickLink("$arbitrary_level log message");
    $this->assertRaw("<th>Type</th><td>$type</td>", "PHPFFMpegLogger::log() should log the message using the type passed as argument to the constructor.");
    $this->assertRaw("<th>Message</th><td>$arbitrary_level log message</td>", "PHPFFMpegLogger::log() should log the message with context substitution.");
    $this->assertRaw("<th>Severity</th><td>notice</td>", "PHPFFMpegLogger::log() should log an arbitrary level message using the 'notice' severity.");

  }

}
