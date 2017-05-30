<?php

namespace Drupal\itk_hamburger_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Entity\Menu;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Provides hamburger menu
 *
 * @Block(
 *   id = "itk_hamburger_menu",
 *   admin_label = @Translation("ITK Hamburger menu"),
 * )
 */
class ItkHamburgerMenu extends BlockBase implements BlockPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function build() {
    // Fetch configuration.
    $config = $this->getConfiguration();

    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters
      ->setRoot('')
      ->excludeRoot()
      ->setMaxDepth($config['itk_hamburger_menu_depth'])
      ->onlyEnabledLinks();
    $variables['menu'] = $menu_tree->load($config['itk_hamburger_menu'], $parameters);
    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $variables['menu'] = $menu_tree->transform($variables['menu'], $manipulators);
    $menu = $menu_tree->build($variables['menu']);

    $menu_html = render($menu);
    return array(
      '#type' => 'markup',
      '#theme' => 'itk_hamburger_menu_block',
      '#attached' => array(
        'library' => array(
          'itk_hamburger_menu/itk_hamburger_menu',
        ),
      ),
      '#cache' => array(
        'max-age' => 0,
      ),
      '#menu' => $menu_html,
    );
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Fetch all site menus for select list.
    $all_menus = Menu::loadMultiple();
    $menus = array();
    foreach ($all_menus as $id => $menu) {
      $menus[$id] = $menu->label();
    }
    asort($menus);

    // Fetch configuration.
    $config = $this->getConfiguration();

    // Menu select list
    $form['itk_hamburger_menu'] = array (
      '#type' => 'select',
      '#title' => $this->t('Menu'),
      '#options' => $menus,
      '#required' => TRUE,
      '#default_value' => isset($config['itk_hamburger_menu']) ? $config['itk_hamburger_menu'] : '',
      '#description' => $this->t('Select the menu you would like to display'),
    );

    // Debth select list
    $form['itk_hamburger_menu_depth'] = [
      '#type' => 'select',
      '#title' => $this->t('Select menu depth'),
      '#options' => [
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3'),
        '4' => $this->t('4'),
      ],
      '#required' => TRUE,
      '#default_value' => isset($config['itk_hamburger_menu_depth']) ? $config['itk_hamburger_menu_depth'] : '',
      '#description' => $this->t('Select the menu depth for the hamburger menu'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('itk_hamburger_menu', $form_state->getValue('itk_hamburger_menu'));
    $this->setConfigurationValue('itk_hamburger_menu_depth', $form_state->getValue('itk_hamburger_menu_depth'));
  }
}
?>