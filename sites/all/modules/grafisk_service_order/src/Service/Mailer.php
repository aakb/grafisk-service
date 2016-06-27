<?php
/**
 * @file
 * Contains the mail service.
 */

namespace Drupal\grafisk_service_order\Service;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;

class Mailer {

  protected $mailManager;

  /**
   * Default construct.
   *
   * @param $mailManager
   *   Mail manager service to send mail with.
   */
  public function __construct($mailManager) {
    $this->mailManager = $mailManager;
  }

  /**
   * Helper function to send mail based on an order.
   *
   * @param string $type
   *   The type of the mail (request, accepted, etc.)
   * @param EntityInterface $order
   *   Order that this mail is about.
   * @param bool $notifyAdmin
   *   Send notification mail to administrator. Defaults to TRUE.
   */
  public function send($type, EntityInterface $order, $notifyAdmin = TRUE) {
    // Get to mail address.
    $to = $order->field_gs_email->value;

    // Generate content.
    $content = (object) $this->generateUserMailContent($type, $order);

    // Send the mail.
    $this->mailer($to, $content->subject, $content->body);

    // Send notification to administrator.
    if ($notifyAdmin) {
      // Generate content.
      $content = (object) $this->generateAdminNotificationMailContent($type, $order);

      // Try to get to address from the site configuration.
      $site_config = \Drupal::config('system.site');
      $to = $site_config->get('mail');
      if (empty($to)) {
        $to = ini_get('sendmail_from');
      }

      // Send the mail.
      $this->mailer($to, $content->subject, $content->body);
    }
  }

  /**
   * Generate mail content for user mails.
   *
   * @param $type
   *   The type of mail message to build.
   * @param EntityInterface $order
   *   The order to use.
   * @return array
   *   Array indexed with "body" and "subject" as keys.
   */
  protected function generateUserMailContent($type, EntityInterface $order) {
    // Build render array for the mail body.
    $messages = \Drupal::getContainer()->get('grafisk_service_order.order_messages');

    switch ($type) {
      case 'create':
        $subject = $messages->get('order_created_email_subject');

        // Build render array.
        $content = array(
          '#theme' => 'order_created_email',
          '#message' => $messages->get('order_created_email_body'),
        );

        break;

      default:
        $subject = 'Unknown mail type';
        $content = array(
          '#type' => 'markup',
          '#message' => 'Error unknown mail type',
        );
        break;
    }

    $this->setTheme($messages->get('email_theme'));

    // Add logo.
    $content += $this->generateLogo();

    // Extend content with order information.
    if (!is_null($order)) {
      $content += $this->generateOrderData($order);
    }

    $subject = $this->replaceTokens($subject, $order);
    $content['#message'] = $this->replaceTokens($content['#message'], $order);

    // Render the body content for the mail.
    return [
      'subject' => $subject,
      'body' => render($content),
    ];
  }

  /**
   * Generate mail content for administrator notification mails.
   *
   * @param $type
   *   The type of mail message to build.
   * @param EntityInterface $order
   *   The order to use.
   * @return array
   *   Array indexed with "body" and "subject" as keys.
   */
  protected function generateAdminNotificationMailContent($type, EntityInterface $order) {
    // Build render array for the mail body.
    $messages = \Drupal::getContainer()->get('grafisk_service_order.order_messages');
    switch ($type) {
      case 'create':
        $subject = $messages->get('order_created_email_subject');

        // Build render array.
        $content = [
          '#theme' => 'order_created_email',
          '#message' => $messages->get('order_created_email_body'),
        ];
        break;

      default:
        $subject = 'Unknown mail type';
        $content = [
          '#type' => 'markup',
          '#message' => 'Error unknown mail type',
        ];
        break;
    }

    $this->setTheme($messages->get('admin_email_theme'));

    // Add logo.
    $content += $this->generateLogo();

    // Extend content with order information.
    if (!is_null($order)) {
      $content += $this->generateOrderData($order);
    }

    $subject = $this->replaceTokens($subject, $order);
    $content['#message'] = $this->replaceTokens($content['#message'], $order);

    // Render the body content for the mail.
    return [
      'subject' => $subject,
      'body' => render($content),
    ];
  }

  /**
   * Set theme for rendering email templates.
   *
   * @param string
   *   The theme name.
   */
  protected function setTheme($themeName) {
    if ($themeName) {
      $theme = \Drupal::service('theme.initialization')->getActiveThemeByName($themeName);
      if ($theme) {
        \Drupal::theme()->setActiveTheme($theme);
      }
    }
  }

  /**
   * Replace tokens in content.
   *
   * @param array $content
   *   The content.
   *
   * @return array
   *   The content with tokens replaced by actual content.
   */
  protected function replaceTokens($content, EntityInterface $order) {
    $token_service = \Drupal::token();
    // Fetch current language for language options.
    $language_interface = \Drupal::languageManager()->getCurrentLanguage();
    // Output the content with tokens replaced.
    return $token_service->replace($content, ['node' => $order], ['langcode' => $language_interface->getId()]);
  }

  /**
   * Generate logo as base64 encode content field.
   *
   * @return array
   *   Array with logo data.
   */
  protected function generateLogo() {
    $content['#logo_url'] = theme_get_setting('logo.url');

    return $content;
  }

  /**
   * Build render array with order information.
   *
   * @param EntityInterface $order
   *   Order to generate data for.
   *
   * @return array
   *   Array with order information.
   */
  protected function generateOrderData(EntityInterface $order) {
    $url = Url::fromRoute('entity.node.canonical', ['node' => $order->id(), 'uuid' => $order->uuid()], ['absolute' => TRUE])->toString();

    $data = [
      'id' => $order->id(),
      'url' => $url,

      'department' => $order->field_gs_department->value,
      'phone' => $order->field_gs_phone->value,
      'contact_person' => $order->field_gs_contact_person->value,
      'email' => $order->field_gs_email->value,

      'title' => $order->title->value,
      'product_type' => $order->field_gs_product_type->value,
      'quantity' => $order->field_gs_quantity->value,
      'comments' => $order->field_gs_comments->value,
      'files' => $order->field_gs_files,

      'ean' => $order->field_gs_ean->value,
      'marketing_account' => $order->field_gs_marketing_account->value,

      'delivery_date' => new \DateTime($order->field_gs_delivery_date->value),
      'delivery_comments' => $order->field_gs_delivery_comments->value,
      'delivery_address' => $order->field_gs_delivery_address->value,
      'delivery_department' => $order->field_gs_delivery_department->value,
      'delivery_zip_code' => $order->field_gs_delivery_zip_code->value,
      'delivery_city' => $order->field_gs_delivery_city->value,
    ];
    return [ '#order' => $data ];
  }

  /**
   * Send HTML mails.
   *
   * @TODO: This is not the Drupal way to send mail, but rather a hack to send
   *        HTML mails. Drupal MailManger service hardcode plain/text as content
   *        type, so HTML is not supported.
   *
   *        When the SwiftMailer module have been ported to D8... USE IT.
   *
   * @param $to
   *   Mail address to send mail to.
   * @param $subject
   *   The mails subject.
   * @param $body
   *   The HTML body content to send.
   * @param string $name
   *   The name of the sender. Defaults to 'Grafisk Service'.
   */
  protected function mailer($to, $subject, $body, $name = 'Grafisk Service') {
    // Try to get from address from the site configuration.
    $site_config = \Drupal::config('system.site');
    $from = $site_config->get('mail');
    if (empty($from)) {
      $from = ini_get('sendmail_from');
    }

    // Get hold of the RAW mailer client.
    $key = Crypt::randomBytesBase64();
    $mailer = $this->mailManager->getInstance([ 'module' => 'grafisk_service_order', 'key' => $key ]);

    // Build mail configuration and set the type to HTML.
    $params = [
      'headers' => array(
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
        'Content-Transfer-Encoding' => '8Bit',
        'X-Mailer' => 'Drupal',
        'Return-Path' => $from,
        'Reply-to' => $from,
        'Sender' => $from,
        'From' => $name . ' <' . $from . '>',
      ),
      'to' => $to,
      'body' => $body,
      'subject' => $subject,
    ];

    // Send the mail.
    $mailer->mail($params);
  }
}