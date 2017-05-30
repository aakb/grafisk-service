<?php
namespace Drupal\grafisk_service_order\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'grafisk_service_order_harvest_data' formatter.
 *
 * @FieldFormatter(
 *   id = "grafisk_service_order_harvest_data",
 *   label = @Translation("Harvest data"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class HarvestDataFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $api = \Drupal::service('grafisk_service_order.harvest_api');

    foreach ($items as $delta => $item) {
      $data = $api->getData($item->value);
      if (isset($data->projectId)) {
        $url = Url::fromUri($api->getProjectUrl($data->projectId));
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $data->projectId,
          '#url' => $url,
          '#attributes' => [
            'target' => 'harvest',
          ],
        ];
      }
    }

    return $elements;
  }

}
