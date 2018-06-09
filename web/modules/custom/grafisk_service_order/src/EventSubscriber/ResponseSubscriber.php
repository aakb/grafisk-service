<?php

namespace Drupal\grafisk_service_order\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface {

  public function removeXFrameOptions(FilterResponseEvent $event) {
    $path = \Drupal::service('path.current')->getPath();
    if ('/ordrer/overblik' === $path) {
      $response = $event->getResponse();
      $response->headers->remove('x-frame-options');
    }
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => ['removeXFrameOptions', -10],
    ];
  }

}
