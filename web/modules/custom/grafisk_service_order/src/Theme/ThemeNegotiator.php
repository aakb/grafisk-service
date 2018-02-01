<?php

namespace Drupal\grafisk_service_order\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 *
 */
class ThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   *
   */
  public function applies(RouteMatchInterface $route) {
    switch ($route->getRouteName()) {
      case 'node.add':
        $action = 'add';
        if ($route->getParameter('node_type')) {
          return $route->getParameter('node_type')->id() == 'gs_order';
        }
        break;

      case 'entity.node.canonical':
        if (\Drupal::currentUser()->isAnonymous()) {
          $node = $route->getParameter('node');
          if ($node && $node->getType() == 'gs_order') {
            return TRUE;
          }
        }
        break;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route) {
    return 'grafisk_service';
  }

}
