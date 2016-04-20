<?php

/**
 * @file
 * Contains \Drupal\grafisk_service_order\Controller\OrderController
 */

namespace Drupal\grafisk_service_order\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for Grafisk service order.
 */
class OrderController {
  public function listAction(Request $request) {



    return [
      '#title' => 'Orders',
    ];
  }
}
