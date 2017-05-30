<?php
/**
 * @file
 * Contains Drupal\itkore_admin\Form\ItkoreSettingsForm.
 */

namespace Drupal\itkore_admin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ItkoreSettingsForm
 *
 * @package Drupal\itkore_admin\Form
 */
class ItkoreSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itkore_settings';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_name = \Drupal::config('system.site')->get('name');
    // Add front page wrapper.
    $form['intro_wrapper'] = array(
      '#title' => $this->t('ITKore specific settings'),
      '#type' => 'item',
      '#description' => $this->t('These pages contain @site_name specific config settings.', array('@site_name' => $site_name)),
      '#weight' => '1',
      '#open' => TRUE,
    );

    // Get local tasks
    $manager = \Drupal::service('plugin.manager.menu.local_task');
    $tasks = $manager->getLocalTasks(\Drupal::routeMatch()->getRouteName(), 0);
    unset($tasks['tabs']['itkore_admin.admin']);

    // Add front page wrapper.
    $form['menu_wrapper'] = array(
      '#title' => $this->t('Configure'),
      '#type' => 'item',
      '#description' => render($tasks['tabs']),
      '#weight' => '2',
      '#open' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');
  }
}

