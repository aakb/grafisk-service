<?php

namespace Drupal\grafisk_service_order\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Harvest\HarvestAPI;
use Harvest\Model\Client;
use Harvest\Model\Project;
use Psr\Log\LoggerInterface;

/**
 * API proxy for Grafisk service harvest.
 */
class HarvestApiProxy {
  protected $configuration;
  protected $logger;

  /**
   * Default construct.
   *
   * Load koba configuration.
   */
  public function __construct(ConfigFactoryInterface $configFactory, LoggerInterface $logger) {
    $this->configuration = $configFactory->get('grafisk_service_order.settings')->get('harvest')['api'];
    $this->logger = $logger;
  }

  private $api;

  /**
   * Get an instance of the HarvestAPI.
   *
   * @return HarvestAPI
   *   The API instance.
   */
  private function getApi() {
    if (!$this->api) {
      $this->api = new HarvestAPI();
      $this->api->setUser($this->configuration['username']);
      $this->api->setPassword($this->configuration['password']);
      $this->api->setAccount($this->configuration['account']);
    }

    return $this->api;
  }

  /**
   * Create a project in Harvest.
   *
   * @param EntityInterface $order
   *   The order.
   *
   * @return int
   *   The id of the created project.
   */
  public function createProject(EntityInterface $order) {
    $clientId = $this->getClientId($order);

    $startsOn = new \DateTime();
    $endsOn = new \DateTime($order->field_gs_delivery_date->value);

    if (!$endsOn || $endsOn < $startsOn) {
      $startsOn = null;
    }

    $project = new Project();
    $project->set('name', $order->getTitle());
    $project->set('client_id', $clientId);
    $project->set('active', true);
    $project->set('code', 'Ny');
    if ($startsOn) {
      $project->set('starts_on', $startsOn->format(\DateTime::ATOM));
    }
    if ($endsOn) {
      $project->set('ends_on', $endsOn->format(\DateTime::ATOM));
    }
    $project->set('notes', $this->getProjectData($order));

    $result = $this->getApi()->createProject($project);

    if (!$result->isSuccess()) {
      $this->logger->error($result->data);
      throw new \Exception('Cannot create project');
    }

    $projectId = $result->data;

    $this->logger->info('HarvestApiProxy.createProject: !clientId !projectId', ['!clientId' => $clientId, '!projectId' => $projectId]);

    return $projectId;
  }

  /**
   * Get a client id for an order.
   *
   * Client names must be unique so we first look for an existing client and
   * then, if not found, we create a new client.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   *   The order.
   *
   * @return int
   *   The id of the created client.
   *
   * @throws \Exception
   */
  private function getClientId(EntityInterface $order) {
    $department = $order->field_gs_department->value;
    $contactPerson = $order->field_gs_contact_person->value;

    $clientName = $department ? $department . ' (Att.: ' . $contactPerson . ')' : $contactPerson;

    $api = $this->getApi();

    if (!$this->clients) {
      $result = $api->getClients();
      if (!$result->isSuccess()) {
        throw new \Exception('Cannot get client list');
      }

      $this->clients = [];

      foreach ($result->data as $client) {
        $this->clients[$client->name] = $client;
      }
    }

    if (isset($this->clients[$clientName])) {
      $client = $this->clients[$clientName];
    } else {
      $client = new Client();
    }

    $client->set('name', $clientName);
    $client->set('details', $this->getClientData($order));

    if ($client->id) {
      $result = $api->updateClient($client);
      if (!$result->isSuccess()) {
        $this->logger->error($result->data);
        throw new \Exception('Cannot update client');
      }

      return $client->id;
    } else {
      $result = $api->createClient($client);
      if (!$result->isSuccess()) {
        $this->logger->error($result->data);
        throw new \Exception('Cannot create client');
      }

      return $result->data;
    }
  }

  private $clients;

  /**
   * Get client data to store in Harvest.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   *   The order.
   *
   * @return array
   *   The data.
   */
  private function getClientData(EntityInterface $order) {
    $debtorNumber = $order->field_gs_marketing_account->value ? '4302 – Markedsføringskonto' : $order->field_gs_ean->value;
    return implode(PHP_EOL, [
        'Tel.: '    . $order->field_gs_phone->value,
        'E-mail: '  . $order->field_gs_email->value,
        'Debitor: ' . $debtorNumber,
      ]);
  }

  /**
   * Get project data to store in Harvest.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   *   The order.
   *
   * @return array
   *   The data.
   */
  private function getProjectData(EntityInterface $order) {
    $nodeUrl = Url::fromRoute('entity.node.canonical', ['node' => $order->id()], ['absolute' => TRUE])->toString();
    $fileUrls = [];
    if ($order->field_gs_files) {
      foreach ($order->field_gs_files as $file) {
        $fileUrls[] = $file->entity->url();
      }
    }

    return implode(PHP_EOL, [
        'Produkttype: ' . $order->field_gs_product_type->value,
        'Antal: ' . $order->field_gs_quantity->value,
        'Kommentar: ' . $order->field_gs_comments->value,
        $fileUrls ? 'Filer: ' . PHP_EOL . implode(PHP_EOL, $fileUrls) : '',
        PHP_EOL,
        $nodeUrl,
      ]);
  }

  /**
   * Get Harvest data stored in an order entity.
   *
   * @param string|\Drupal\Core\Entity\EntityInterface $order
   *   The order or order data (json).
   *
   * @return array
   *   The Harvest data.
   */
  public function getData($order) {
    $value = is_string($order) ? $order : $order->field_gs_harvest_data->value;
    return json_decode($value);
  }

  /**
   * Get Harvest project url.
   *
   * @param int $projectId
   *   The Harvest project id.
   *
   * @return string
   *   The project url.
   */
  public function getProjectUrl($projectId) {
    return 'https://' . $this->configuration['account'] . '.harvestapp.com/projects/' . $projectId;
  }

}
