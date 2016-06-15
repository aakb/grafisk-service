<?php
/**
 * @file
 * Contains key/value storage for order messages.
 */

namespace Drupal\grafisk_service_order\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class OrderMessages extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('grafisk_service_order.order_messages', $serializer, $connection);
  }
}
