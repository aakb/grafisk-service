<?php

namespace Drupal\grafisk_service_order\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Harvest\HarvestApi;
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

  private function getApi() {
    if (!$this->api) {
      $this->api = new HarvestApi();
      $this->api->setUser($this->configuration['username']);
      $this->api->setPassword($this->configuration['password']);
      $this->api->setAccount($this->configuration['account']);
    }

    return $this->api;
  }

  public function createProject(EntityInterface $order) {
    $clientId = $this->getClientId($order);

    $nodeUrl = Url::fromRoute('entity.node.canonical', [ 'node' => $order->id() ], [ 'absolute' => true ])->toString();

    $project = new Project();
    $project->set('name', $order->getTitle() . ' (#' . $order->id() . ')'); // Project names must be unique.
    $project->set('client_id', $clientId);
    $project->set('active', TRUE);
    $project->set('notes', '[' . $nodeUrl . ']' . PHP_EOL . PHP_EOL . '*** Do not edit below this line! ***' . PHP_EOL . '---' . PHP_EOL . json_encode($this->getOrderData($order)));

    $result = $this->getApi()->createProject($project);

    if (!$result->isSuccess()) {
      $this->logger->error($result->data);
      throw new \Exception('Cannot create project');
    }

    $projectId = $result->data;

    $this->logger->info(json_encode([ $clientId, $projectId ]));

    return $projectId;
  }

  /**
   * Get a client id for an order.
   *
   * Client names must be unique so we first look for an existing client and then, if not found, we create a new client.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   * @return mixed
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
      return $this->clients[$clientName]->id;
    }

    $client = new Client();
    $client->set('name', $clientName);
    $client->set('details', 'xxxx' . $order->field_gs_address->value . PHP_EOL . $order->field_gs_city->value);

    $result = $api->createClient($client);

    if (!$result->isSuccess()) {
      $this->logger->error($result->data);
      throw new \Exception('Cannot create client');
    }

    return $result->data;
  }

  private $clients;

  /**
   * Get order data to store in Harvest.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   * @return array
   */
  private function getOrderData(EntityInterface $order) {
    // @TODO: What do we send to Harvest?
    return [];
  }

  public function getProjectUrl($projectId) {
    return 'https://' . $this->configuration['account'] . '.harvestapp.com/projects/' . $projectId;
  }
}
