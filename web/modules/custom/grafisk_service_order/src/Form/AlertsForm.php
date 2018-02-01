<?php

namespace Drupal\grafisk_service_order\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContentEntityExampleSettingsForm.
 *
 * @package Drupal\grafisk_service_order\Form
 * @ingroup grafisk_service_order
 */
class AlertsForm extends FormBase {
  protected $dateFormatter;

  /**
   *
   */
  public function __construct(DateFormatterInterface $dateFormatter) {
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'grafisk_service_order_alerts';
  }

  /**
   * Get key/value storage for order messages.
   *
   * @return object
   */
  private function getSettings() {
    return \Drupal::getContainer()->get('grafisk_service_order.alerts');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();

    $form['order_age_settings'] = [
      '#title' => $this->t('Alert age settings'),
      '#type' => 'fieldset',
    ];

    $periods = [
      1 * 60 * 60,
      2 * 60 * 60,
      6 * 60 * 60,
      12 * 60 * 60,
      24 * 60 * 60,
    ];
    $options = array_map([$this->dateFormatter, 'formatInterval'], array_combine($periods, $periods));

    $form['order_age_settings']['order_max_age'] = [
      '#type' => 'select',
      '#title' => $this->t('Order max age'),
      '#description' => $this->t('Max age of order before alert is issued.'),
      '#required' => TRUE,
      '#options' => $options,
      '#default_value' => $settings->get('order_max_age', 24 * 60 * 60),
    ];

    $form['email_settings'] = [
      '#title' => $this->t('Alert email settings'),
      '#type' => 'fieldset',
    ];

    $form['email_settings']['email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alert email subject'),
      '#required' => TRUE,
      '#default_value' => $settings->get('email_subject'),
    ];

    $form['email_settings']['email_recipients'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Alert email recipients (one per line)'),
      '#required' => TRUE,
      '#default_value' => $settings->get('email_recipients'),
    ];

    $form['settings_submit'] = [
      '#type' => 'submit',
      '#value' => t('Save alert settings'),
    ];

    return $form;
  }

  /**
   * Form submission handler for email config.
   *
   * @param $form
   *   An associative array containing the structure of the form.
   * @param $form_state
   *   The current state of the form.
   */
  public function alertSettingsSubmit(array $form, FormStateInterface $form_state) {
    drupal_set_message($this->t('Alert settings saved'));
    $this->getSettings()->setMultiple([
      'time' => $form_state->getValue('time'),
      'email_subject' => $form_state->getValue('email_subject'),
      'email_recipients' => $form_state->getValue('email_recipients')['value'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('Alert settings saved'));
    $this->getSettings()->setMultiple([
      'order_max_age' => $form_state->getValue('order_max_age'),
      'email_subject' => $form_state->getValue('email_subject'),
      'email_recipients' => $form_state->getValue('email_recipients'),
    ]);
  }

}
