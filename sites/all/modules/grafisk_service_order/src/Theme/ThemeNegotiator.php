<?php
/**
 * @file
 * Contains \Drupal\grafisk_service_order\Theme\ThemeNegotiator.
 */

namespace Drupal\grafisk_service_order\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class ThemeNegotiator implements ThemeNegotiatorInterface {

  public function applies(RouteMatchInterface $route) {
    switch ($route->getRouteName()) {
      case 'node.add':
        $action = 'add';
        if ($route->getParameter('node_type')) {
          return $route->getParameter('node_type')->id() == 'gs_order';
        }
        break;
    }

    return false;

    $account = \Drupal::currentUser();

    return $account->id() === 0;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return 'grafisk_service';
 }
}