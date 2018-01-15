<?php

/**
 * @file
 * Contains Drupal\grafisk_service_order\Plugin\Field\FieldType\OrderLineItem.
 */

namespace Drupal\grafisk_service_order\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * Plugin implementation of the 'grafisk_service_order_order_line' field type.
 *
 * @FieldType(
 *   id = "grafisk_service_order_order_line",
 *   label = @Translation("Order line"),
 *   description = @Translation("This field stores data for an order line."),
 *   default_widget = "grafisk_service_order_order_line",
 *   default_formatter = "grafisk_service_order_order_line"
 * )
 */
class OrderLineItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'quantity' => [
          'description' => 'Stores the quantity',
          'type' => 'varchar',
          'length' => '255',
          'not null' => TRUE,
        ],
        'product_type' => [
          'description' => 'Stores the product type',
          'type' => 'varchar',
          'length' => '255',
          'not null' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['quantity'] = DataDefinition::create('string')
                            ->setLabel(t('Quantity'));

    $properties['product_type'] = DataDefinition::create('string')
                         ->setLabel(t('Product type'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $quantity = $this->get('quantity')->getValue();
    return $quantity === NULL || $quantity === '';
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    $this->quantity = trim($this->quantity);
    $this->product_type = trim($this->product_type);
  }

}
