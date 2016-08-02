<?php

/**
 * @file
 * Contains \Drupal\php_ffmpeg\Form\PhpFFMpegSettings.
 */

namespace Drupal\php_ffmpeg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class PhpFFMpegSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'php_ffmpeg_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['php_ffmpeg.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];
    $form['php_ffmpeg_ffmpeg_binary'] = [
      '#type' => 'textfield',
      '#title' => t('ffmpeg binary'),
      '#description' => t('Path to the ffmpeg binary. Leave empty if the binary is located within system PATH.'),
      '#default_value' => $this->config('php_ffmpeg.settings')->get('php_ffmpeg_ffmpeg_binary'),
    ];
    $form['php_ffmpeg_ffprobe_binary'] = [
      '#type' => 'textfield',
      '#title' => t('ffprobe binary'),
      '#description' => t('Path to the ffprobe binary. Leave empty if the binary is located within system PATH.'),
      '#default_value' => $this->config('php_ffmpeg.settings')->get('php_ffmpeg_ffprobe_binary'),
    ];
    $form['php_ffmpeg_timeout'] = [
      '#type' => 'textfield',
      '#title' => t('Timeout'),
      '#description' => t('Timeout for ffmpeg/ffprobe command execution in seconds. Leave empty for none.'),
      '#default_value' => $this->config('php_ffmpeg.settings')->get('php_ffmpeg_timeout'),
    ];
    $form['php_ffmpeg_threads'] = [
      '#type' => 'textfield',
      '#title' => t('Threads'),
      '#description' => t('Number of threads to use for ffmpeg commands.'),
      '#default_value' => $this->config('php_ffmpeg.settings')->get('php_ffmpeg_threads'),
    ];
    if (function_exists('monolog_channel_info_load_all') && ($channels = monolog_channel_info_load_all())) {
      $channel_options = [NULL => t('-None-')] + array_map(function($channel) {
        return $channel['label'];
      }, $channels);
      $form['php_ffmpeg_monolog_channel'] = [
        '#type' => 'select',
        '#title' => t('Monolog channel'),
        '#description' => t('Select the monolog channel to use for logging.'),
        '#default_value' => $this->config('php_ffmpeg.settings')->get('php_ffmpeg_monolog_channel'),
        '#options' => $channel_options,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if ($form_state->getValue(['php_ffmpeg_ffmpeg_binary']) && !file_exists($form_state->getValue(['php_ffmpeg_ffmpeg_binary']))) {
      $form_state->setErrorByName('php_ffmpeg_ffmpeg_binary', t('File not found: @file', [
        '@file' => $form_state->getValue(['php_ffmpeg_ffmpeg_binary'])
        ]));
    }
    if ($form_state->getValue(['php_ffmpeg_ffprobe_binary']) && !file_exists($form_state->getValue(['php_ffmpeg_ffprobe_binary']))) {
      $form_state->setErrorByName('php_ffmpeg_ffprobe_binary', t('File not found: @file', [
        '@file' => $form_state->getValue(['php_ffmpeg_ffprobe_binary'])
        ]));
    }
    if ($form_state->getValue(['php_ffmpeg_timeout']) && (!is_numeric($form_state->getValue(['php_ffmpeg_timeout'])) || $form_state->getValue(['php_ffmpeg_timeout']) < 0 || (intval($form_state->getValue(['php_ffmpeg_timeout'])) != $form_state->getValue(['php_ffmpeg_timeout'])))) {
      $form_state->setErrorByName('php_ffmpeg_timeout', t('The value of the Timeout field must be a positive integer.'));
    }
    if ($form_state->getValue(['php_ffmpeg_threads']) && (!is_numeric($form_state->getValue(['php_ffmpeg_threads'])) || $form_state->getValue(['php_ffmpeg_threads']) < 0) || (intval($form_state->getValue(['php_ffmpeg_threads'])) != $form_state->getValue(['php_ffmpeg_threads']))) {
      $form_state->setErrorByName('php_ffmpeg_threads', t('The value of the Threads field must be zero or a positive integer.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('php_ffmpeg.settings')->set('php_ffmpeg_ffmpeg_binary', $form_state->getValue('php_ffmpeg_ffmpeg_binary'))
      ->set('php_ffmpeg_ffprobe_binary', $form_state->getValue('php_ffmpeg_ffprobe_binary'))
      ->set('php_ffmpeg_timeout', $form_state->getValue('php_ffmpeg_timeout'))
      ->set('php_ffmpeg_threads', $form_state->getValue('php_ffmpeg_threads'))
      ->set('php_ffmpeg_ffmpeg_binary', $form_state->getValue('php_ffmpeg_ffmpeg_binary'))
      ->save();
  }

}
