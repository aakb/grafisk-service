<?php

/**
 * @file
 * Contains \Drupal\grafisk_service_order\Plugin\Field\FieldWidget\OrderLineWidget.
 */

namespace Drupal\grafisk_service_order\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'grafisk_service_order_order_line' widget.
 *
 * @FieldWidget(
 *   id = "grafisk_service_order_order_line",
 *   label = @Translation("Order line"),
 *   field_types = {
 *     "grafisk_service_order_order_line"
 *   }
 * )
 */
class OrderLineWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['quantity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Quantity'),
      '#empty_value' => '',
      '#default_value' => (isset($items[$delta]->quantity)) ? $items[$delta]->quantity : NULL,
      '#maxlength' => 255,
    ];

    $element['product_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Product type'),
      '#empty_value' => '',
      '#default_value' => (isset($items[$delta]->product_type)) ? $items[$delta]->product_type : NULL,
      '#maxlength' => 255,
      '#attributes' => [
        'placeholder' => 'Skriv f.eks. folder, plakat, visitkort …',
      ],
    ];

    return $element;
  }

}
