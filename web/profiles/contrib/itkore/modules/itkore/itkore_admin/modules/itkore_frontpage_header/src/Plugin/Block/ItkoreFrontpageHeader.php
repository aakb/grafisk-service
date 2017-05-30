<?php

namespace Drupal\itkore_frontpage_header\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides frontpage header
 *
 * @Block(
 *   id = "itkore_frontpage_header",
 *   admin_label = @Translation("ITKore frontpage header"),
 * )
 */
class ItkoreFrontpageHeader extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itkore_admin.itkore_config')->getAll();

    // Fetch header top file.
    $file = ($config['frontpage_image']) ? File::load($config['frontpage_image']) : FALSE;
    $config['frontpage_image_url'] = $file ? $file->url() : '';

    return array(
      '#type' => 'markup',
      '#theme' => 'itkore_frontpage_header_block',
      '#cache' => array(
        'max-age' => 0,
      ),
      '#variables' => $config,
    );
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Menu select list
    $form['item'] = array(
      '#type' => 'item',
      '#description' => t('NOTE: Part of the configuration for this block is found in <a href="/admin/site-setup/frontpage-header">site settings</a>'),
      '#weight' => '0',
    );

    return $form;
  }
}
?>