<?php

/**
 * @file
 * Contains \Drupal\itkore\Form\CustomInstallForm
 */
namespace Drupal\itkore\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements custom config form for itkore
 */
class CustomInstallForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itkore_install_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = t('Setup custom ITKore config');
    $form['itkore_install_modules'] = array(
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => 'ITKore module setup',
      '#weight' => -1,
      '#description' => 'Enable custom modules',
      'modules' => array(
        '#type' => 'checkboxes',
        '#options' => array(
          'itk_admin_links' => 'ITK admin links tool',
          'itk_cookie_message' => 'ITK cookie message',
          'itkore_user_roles' => 'ITK default user roles and permissions',
          'itkore_user_theme' => 'ITK admin theme for user login pages',
          'itkore_content_types' => 'Paragraphs, fields and default content types (Page, overview page, news)',
          'itk_hamburger_menu' => 'ITK hamburger menu',
          'itkore_frontpage_header' => 'ITK frontpage header block with configurable ui',
          'itkore_footer' => 'ITK footer block with configurable ui',
        ),
        '#default_value' => array(
          'itk_admin_links',
          'itk_cookie_message',
          'itkore_user_roles',
          'itkore_user_theme',
          'itkore_content_types',
          'itk_hamburger_menu',
          'itkore_frontpage_header',
          'itkore_footer',
        ),
      ),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save and continue'),
      '#button_type' => 'primary',
    );
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Setup  install state array for saving data. (Copied from core)
    global $install_state;

    $form_values = $form_state->getValues();
    $modules = array();

    // Save chosen modules to install state.
    foreach($form_values['modules'] as $key => $value) {
      if ($key === $value) {
        $modules[] = $value;
      }
    }
    
    $install_state['custom_modules'] = $modules;
  }
}