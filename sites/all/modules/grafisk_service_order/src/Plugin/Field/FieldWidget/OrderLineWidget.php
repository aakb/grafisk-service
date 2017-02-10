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
  public static function defaultSettings() {
    return [
      'quantity' => [
        'size' => 10,
        'placeholder' => '',
      ],
      'product_type' => [
        'size' => 80,
        'placeholder' => '',
      ],
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [
      'quantity' => [
        '#tree' => TRUE,
        '#type' => 'fieldset',
        '#title' => t('Quantity settings'),

        'size' => [
          '#type' => 'number',
          '#title' => t('Size of textfield'),
          '#default_value' => $this->getSetting('quantity')['size'],
          '#required' => TRUE,
          '#min' => 1,
        ],

        'placeholder' => [
          '#type' => 'textfield',
          '#title' => t('Placeholder'),
          '#default_value' => $this->getSetting('quantity')['placeholder'],
          '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
        ],
      ],

      'product_type' => [
        '#tree' => TRUE,
        '#type' => 'fieldset',
        '#title' => t('Product_Type settings'),

        'size' => [
          '#type' => 'number',
          '#title' => t('Size of textfield'),
          '#default_value' => $this->getSetting('product_type')['size'],
          '#required' => TRUE,
          '#min' => 1,
        ],

        'placeholder' => [
          '#type' => 'textfield',
          '#title' => t('Placeholder'),
          '#default_value' => $this->getSetting('product_type')['placeholder'],
          '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
        ],
      ],
    ];

    return $element;
  }

  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Quantity');
    $summary[] = '- ' . t('Textfield size: @size', array('@size' => $this->getSetting('quantity')['size']));
    $summary[] = '- ' . t('Placeholder: @placeholder', array('@placeholder' => $this->getSetting('quantity')['placeholder']));

    $summary[] = t('Product type');
    $summary[] = '- ' . t('Textfield size: @size', array('@size' => $this->getSetting('product_type')['size']));
    $summary[] = '- ' . t('Placeholder: @placeholder', array('@placeholder' => $this->getSetting('product_type')['placeholder']));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['quantity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Quantity'),
      '#empty_value' => '',
      '#default_value' => (isset($items[$delta]->quantity)) ? $items[$delta]->quantity : NULL,
      '#maxlength' => $this->getSetting('quantity')['size'],
      '#attributes' => [
        'placeholder' => $this->getSetting('quantity')['placeholder'],
      ],
    ];

    $element['product_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Product type'),
      '#empty_value' => '',
      '#default_value' => (isset($items[$delta]->product_type)) ? $items[$delta]->product_type : NULL,
      '#maxlength' => $this->getSetting('product_type')['size'],
      '#attributes' => [
        'placeholder' => $this->getSetting('product_type')['placeholder'],
      ],
    ];

    return $element;
  }

}
