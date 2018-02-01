<?php

namespace Drupal\grafisk_service_order\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'grafisk_service_order_order_line' formatter.
 *
 * @FieldFormatter(
 *   id = "grafisk_service_order_order_line",
 *   module = "grafisk_service_order_order_line",
 *   label = @Translation("Order line"),
 *   field_types = {
 *     "grafisk_service_order_order_line"
 *   }
 * )
 */
class OrderLineFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'grafisk_service_order_order_line_formatter',
        '#quantity' => $item->quantity,
        '#product_type' => $item->product_type,
      ];
    }

    return $element;
  }

}
