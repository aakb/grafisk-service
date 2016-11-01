<?php

namespace Drupal\grafisk_service_order\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An example controller.
 */
class OrderController extends ControllerBase {
  public static function create(ContainerInterface $container) {
    return new static($container);
  }

  private $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public function addressLabelAction($id) {
    $order = \Drupal\node\Entity\Node::load($id);

    $build = [
      '#theme' => 'order_address_label',
      '#order' => $order,
      '#template_path' => '/' . drupal_get_path('module', 'grafisk_service_order') . '/templates',
    ];

    $content = \Drupal::service('renderer')->renderRoot($build);

    echo $content; die;
  }
}
