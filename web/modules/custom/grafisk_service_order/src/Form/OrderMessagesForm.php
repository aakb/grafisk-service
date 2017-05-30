<?php
/**
 * @file
 * Contains Drupal\grafisk_service_order\Form\OrderMessagesForm.
 */

namespace Drupal\grafisk_service_order\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class ContentEntityExampleSettingsForm.
 * @package Drupal\grafisk_service_order\Form
 * @ingroup grafisk_service_order
 */
class OrderMessagesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'grafisk_service_order_messages';
  }

  /**
   * Get key/value storage for order messages.
   *
   * @return object
   */
  private function getSettings() {
    return \Drupal::getContainer()->get('grafisk_service_order.order_messages');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $tokens_description = t('Available tokens are: [node:title]');

    // Email theme settings.
    $installed_themes = array_filter(\Drupal::service('theme_handler')->rebuildThemeData(), function($theme) {
      return $theme->status;
    });
    $installed_themes_options = array('' => '');
    foreach ($installed_themes as $name => $theme) {
      $installed_themes_options[$name] = $theme->info['name'];
    }

    $form['user_email_settings_wrapper'] = array(
      '#title' => $this->t('User email settings'),
      '#type' => 'fieldset',
      '#weight' => '1',
    );

    // User email settings.
    $form['user_email_settings_wrapper']['user_email_settings'] = array(
      '#type' => 'vertical_tabs',
      '#description' => t('Messages sent to the users email address.'),
    );

    // Email settings.
    $form['user_email_settings']['order_created_email'] = array(
      '#title' => $this->t('Order created (User)'),
      '#type' => 'details',
      '#weight' => '1',
      '#group' => 'user_email_settings',
    );

    $form['user_email_settings']['order_created_email']['order_created_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email subject'),
      '#default_value' => $settings->get('order_created_email_subject'),
    );

    $form['user_email_settings']['order_created_email']['order_created_email_body'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Email body'),
      '#default_value' => $settings->get('order_created_email_body'),
      '#description' => $tokens_description,
    );

    $form['user_email_settings']['order_created_email']['order_created_email_from'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Email sender'),
      '#default_value' => $settings->get('order_created_email_from'),
    );

    $form['user_email_settings']['order_created_email']['order_created_email_from_name'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Email sender name'),
      '#default_value' => $settings->get('order_created_email_from_name'),
    );

    $form['user_email_settings']['order_created_email']['order_created_email_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save order created email settings'),
      '#weight' => 1,
      '#submit' => array('::order_created_email_submit'),
    );

    $form['user_email_settings']['theme'] = array(
      '#title' => $this->t('Theme'),
      '#type' => 'details',
      '#weight' => '87',
      '#group' => 'user_email_settings',
    );

    $form['user_email_settings']['theme']['email_theme'] = array(
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#options' => $installed_themes_options,
      '#default_value' => $settings->get('email_theme'),
      '#description' => t('Theme used for sending user emails'),
    );

    $form['user_email_settings']['theme']['theme_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save email theme settings'),
      '#weight' => 1,
      '#submit' => array('::theme_submit'),
    );

    // Admin settings.
    $form['admin_email_settings_wrapper'] = array(
      '#title' => $this->t('Admin email settings'),
      '#type' => 'fieldset',
      '#weight' => '2',
    );

    // Admin emails settings.
    $form['admin_email_settings_wrapper']['admin_email_settings'] = array(
      '#type' => 'vertical_tabs',
      '#description' => t('Messages sent to a shared administration email account.'),
    );

    // Email settings.
    $form['admin_email_settings']['admin_order_created_email'] = array(
      '#title' => $this->t('Order created (Admin)'),
      '#type' => 'details',
      '#weight' => 1,
      '#group' => 'admin_email_settings',
    );

    $form['admin_email_settings']['admin_order_created_email']['admin_order_created_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email subject'),
      '#default_value' => $settings->get('admin_order_created_email_subject'),
    );

    $form['admin_email_settings']['admin_order_created_email']['admin_order_created_email_body'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Email body'),
      '#default_value' => $settings->get('admin_order_created_email_body'),
      '#description' => $tokens_description,
    );

    $form['admin_email_settings']['admin_order_created_email']['admin_order_created_email_to'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Email recipient'),
      '#default_value' => $settings->get('admin_order_created_email_to'),
    );

    $form['admin_email_settings']['admin_order_created_email']['admin_order_created_email_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save admin email settings'),
      '#weight' => 1,
      '#submit' => array('::admin_order_created_email_submit'),
    );

    // Admin email theme settings.
    $form['admin_email_settings']['admin_theme'] = array(
      '#title' => $this->t('Theme'),
      '#type' => 'details',
      '#weight' => '87',
      '#group' => 'admin_email_settings',
    );

    $form['admin_email_settings']['admin_theme']['admin_email_theme'] = array(
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#options' => $installed_themes_options,
      '#default_value' => $settings->get('admin_email_theme'),
      '#description' => t('Theme used for sending admin emails'),
    );

    $form['admin_email_settings']['admin_theme']['admin_theme_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save admin email theme settings'),
      '#weight' => 1,
      '#submit' => array('::admin_theme_submit'),
    );


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
  public function order_created_email_submit(array $form, FormStateInterface $form_state) {
    drupal_set_message('Email settings saved');
    $this->getSettings()->setMultiple(array(
      'order_created_email_subject' => $form_state->getValue('order_created_email_subject'),
      'order_created_email_body' => $form_state->getValue('order_created_email_body')['value'],
      'order_created_email_from' => $form_state->getValue('order_created_email_from'),
      'order_created_email_from_name' => $form_state->getValue('order_created_email_from_name'),
    ));
  }

  /**
   * Form submission handler for email theme config.
   *
   * @param $form
   *   An associative array containing the structure of the form.
   * @param $form_state
   *   The current state of the form.
   */
  public function theme_submit(array $form, FormStateInterface $form_state) {
    drupal_set_message('Email theme settings saved');
    $this->getSettings()->setMultiple(array(
      'email_theme' => $form_state->getValue('email_theme'),
    ));
  }

  /**
   * Form submission handler for email config.
   *
   * @param $form
   *   An associative array containing the structure of the form.
   * @param $form_state
   *   The current state of the form.
   */

  public function admin_order_created_email_submit(array $form, FormStateInterface $form_state) {
    drupal_set_message('Admin email settings saved');
    $this->getSettings()->setMultiple(array(
      'admin_order_created_email_subject' => $form_state->getValue('admin_order_created_email_subject'),
      'admin_order_created_email_body' => $form_state->getValue('admin_order_created_email_body')['value'],
      'admin_order_created_email_to' => $form_state->getValue('admin_order_created_email_to'),
      ));
  }

  /**
   * Form submission handler for email admin theme config.
   *
   * @param $form
   *   An associative array containing the structure of the form.
   * @param $form_state
   *   The current state of the form.
   */
  public function admin_theme_submit(array $form, FormStateInterface $form_state) {
    drupal_set_message('Email theme settings saved');
    $this->getSettings()->setMultiple(array(
      'admin_email_theme' => $form_state->getValue('admin_email_theme'),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
}
