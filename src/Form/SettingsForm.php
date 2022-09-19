<?php

namespace Drupal\largefiles\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide settings for Stage File Proxy.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The site path.
   *
   * @var string
   */
  protected $sitePath;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, string $site_path) {
    parent::__construct($config_factory);

    $this->sitePath = $site_path;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('site.path')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'largefiles_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'largefiles.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_type = NULL) {
    // TODO: how to set to 2mb by default?
    $config = $this->config('largefiles.settings');

    if ($config->get('file_size') === '') {
      $default = 2;
    } else {
      $default = $config->get('file_size');
    }

    $form['file_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('File size (in MB)'),
      '#default_value' => $default,
      '#description' => $this->t("The smallest file size to check for. Default is 2mb. So module will check for and display list of files 2mb and larger."),
      '#required' => TRUE,
      '#size' => 5,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $file_size = $form_state->getValue('file_size');

    if (!empty($file_size) === FALSE) {
      $form_state->setErrorByName('file_size', 'Please fill out file size.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('largefiles.settings');

    $keys = [
      'file_size',
    ];
    foreach ($keys as $key) {
      $value = $form_state->getValue($key);
      $config->set($key, $value);
    }
    $config->save();
    $this->messenger()->addMessage($this->t('Your settings have been saved.'));
  }

}
