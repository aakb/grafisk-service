<?php
/**
 * @file
 * Contains \Drupal\grafisk_service_order\Plugin\Field\FieldFormatter\HarvestProjectIdFormatter.
 */

namespace Drupal\grafisk_service_order\Plugin\Field\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'grafisk_service_order_project_id' formatter.
 *
 * @FieldFormatter(
 *   id = "grafisk_service_order_project_id",
 *   label = @Translation("Harvest project id"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class HarvestProjectIdFormatter extends StringFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $api = \Drupal::service('grafisk_service_order.harvest_api');

    foreach ($items as $delta => $item) {
      $url = Url::fromUri($api->getProjectUrl($item->value));
      $elements[$delta] = [
        '#type' => 'link',
        '#title' => $item->value,
        '#url' => $url,
        '#attributes' => [
          'target' => 'harvest',
        ]
      ];
    }
    return $elements;
  }
}
