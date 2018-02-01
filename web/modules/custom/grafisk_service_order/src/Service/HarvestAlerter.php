<?php

namespace Drupal\grafisk_service_order\Service;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\grafisk_service_order\State\Alerts;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;

/**
 * API proxy for Grafisk service harvest.
 */
class HarvestAlerter {
  protected $configuration;
  protected $mailManager;
  protected $twig;
  protected $logger;

  /**
   * Default construct.
   *
   * Load koba configuration.
   */
  public function __construct(Alerts $configuration, MailManagerInterface $mailManager, \Twig_Environment $twig, LoggerInterface $logger) {
    $this->configuration = $configuration;
    $this->mailManager = $mailManager;
    $this->twig = $twig;
    $this->logger = $logger;
  }

  public function cron() {
    $maxAge = max($this->configuration->get('order_max_age', 24 * 60 * 60), 60 * 60);
    $createdBefore = time() - $maxAge;

    $query = \Drupal::entityQuery('node')
      ->condition('type', GS_ORDER_NODE_TYPE)
      ->condition('field_gs_harvest_data', '{}', '=')
      ->condition('created', $createdBefore, '<')
      ->condition('status', Node::PUBLISHED);
    $ids = $query->execute();
    $orders = Node::loadMultiple(array_values($ids));

    if (!empty($orders)) {
      $message = $this->render('orders-not-exported.txt.twig', [
        'orders' => $orders,
      ]);
      $this->sendAlert($message);
      var_export($message);
    }

    $this->logger->info(__METHOD__);
  }

  private function sendAlert($message) {
    $siteConfig = \Drupal::config('system.site');
    $from = $siteConfig->get('mail') ?: ini_get('sendmail_from');
    $to = $this->configuration->get('email_recipients');
    $subject = $this->configuration->get('email_subject');
    $body = $message;

    // Get hold of the RAW mailer client.
    $key = Crypt::randomBytesBase64();
    $mailer = $this->mailManager->getInstance([ 'module' => 'grafisk_service_order', 'key' => $key ]);

    // Build mail configuration and set the type to HTML.
    $params = [
      'headers' => array(
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
        'Content-Transfer-Encoding' => '8Bit',
        'X-Mailer' => 'Drupal',
        'Return-Path' => $from,
        'Reply-to' => $from,
        'Sender' => $from,
        'From' => $from,
      ),
      'to' => $to,
      'body' => $body,
      'subject' => $subject,
    ];

    // Send the mail.
    $mailer->mail($params);
  }

  /**
   *
   */
  private function render($templateName, $data) {
    $data += [
      'base_url' => \Drupal::request()->getSchemeAndHttpHost(),
    ];
    $templatePath = DRUPAL_ROOT . '/' . drupal_get_path('module', 'grafisk_service_order') . '/templates/alert/' . $templateName;
    $template = file_get_contents($templatePath);
    $content = $this->twig->createTemplate($template)->render($data);

    return $content;
  }

}
