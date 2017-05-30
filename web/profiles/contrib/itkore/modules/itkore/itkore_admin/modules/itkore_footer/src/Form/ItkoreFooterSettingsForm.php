<?php
/**
 * @file
 * Contains Drupal\itkore_frontpage_header\Form\ItkoreFrontpageHeaderSettingsForm.
 */

namespace Drupal\itkore_footer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\system\Entity\Menu;
use Drupal\Core\Session;

/**
 * Class ItkoreFooterSettingsForm
 *
 * @package Drupal\itkore_admin\Form
 */
class ItkoreFooterSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itkore_footer_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itkore_admin.itkore_config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    // Fetch all site menus for select list.
    $all_menus = Menu::loadMultiple();
    $menus = array();
    foreach ($all_menus as $id => $menu) {
      $menus[$id] = $menu->label();
    }
    asort($menus);

    $form['footer_text'] = array(
      '#title' => $this->t('Text'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('footer_text'),
      '#weight' => '1',
    );

    $form['footer_menus'] = array(
      '#title' => $this->t('Menus'),
      '#type' => 'checkboxes',
      '#options' => $menus,
      '#default_value' => ($config->get('footer_menus')) ? $config->get('footer_menus') : array(),
      '#weight' => '2',
      '#access' => \Drupal::currentUser()->hasPermission('administer site configuration'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save changes'),
      '#weight' => '6',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');

    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple(array(
      'footer_text' => $form_state->getValue('footer_text')['value'],
      'footer_menus' => $form_state->getValue('footer_menus'),
    ));

    drupal_flush_all_caches();
  }
}

