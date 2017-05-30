<?php

namespace Drupal\itkore_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides footer content
 *
 * @Block(
 *   id = "itkore_footer",
 *   admin_label = @Translation("ITKore footer"),
 * )
 */
class ItkoreFooter extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itkore_admin.itkore_config')->getAll();
    $menus = array();
    $menu_tree = \Drupal::menuTree();
    
    if (isset($config['footer_menus'])) {
      foreach ($config['footer_menus'] as $key => $value) {
        if ($key ===  $value) {
          $parameters = new MenuTreeParameters();
          $parameters
            ->setRoot('')
            ->excludeRoot()
            ->onlyEnabledLinks();
          $variables['menu'][$key] = $menu_tree->load($value, $parameters);
          $manipulators = array(
            array('callable' => 'menu.default_tree_manipulators:checkAccess'),
            array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
            array('callable' => 'menu.default_tree_manipulators:flatten'),
          );
          $variables['menu'][$key] = $menu_tree->transform($variables['menu'][$key], $manipulators);
          $menu[$key] = $menu_tree->build($variables['menu'][$key]);
          $menus[$key] = render($menu[$key]);
        }
      }

      $footer_text = check_markup($config['footer_text'], 'filtered_html');

      return array(
        '#type' => 'markup',
        '#theme' => 'itkore_footer_block',
        '#cache' => array(
          'max-age' => 0,
        ),
        '#footer_text' => $footer_text,
        '#menus' => $menus,
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Menu select list
    $form['item'] = array (
      '#type' => 'item',
      '#description' => t('NOTE: Part of the configuration for this block is found in <a href="/admin/site-setup/footer">site settings</a>'),
      '#weight' => '0',
    );

    return $form;
  }
}
?>