<?php

namespace Drupal\grafisk_service_order\Controller;

use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An example controller.
 */
class OrderController extends ControllerBase {

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static($container);
  }

  private $container;

  /**
   *
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public function addressLabelAction($id) {
    $order = Node::load($id);
    $harvestData = $order->field_gs_harvest_data->value ? json_decode($order->field_gs_harvest_data->value) : NULL;

    $build = [
      '#theme' => 'order_address_label',
      '#order' => $order,
      '#harvest_data' => $harvestData,
      '#template_path' => '/' . drupal_get_path('module', 'grafisk_service_order') . '/templates',
    ];

    $content = \Drupal::service('renderer')->renderRoot($build);

    echo $content; die;
  }

}
