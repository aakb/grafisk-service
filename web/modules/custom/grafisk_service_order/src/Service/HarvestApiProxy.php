<?php

namespace Drupal\grafisk_service_order\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Harvest\HarvestAPI;
use Harvest\Model\Client;
use Harvest\Model\Invoice\Filter;
use Harvest\Model\Project;
use Psr\Log\LoggerInterface;

/**
 * API proxy for Grafisk service harvest.
 */
class HarvestApiProxy {
  protected $configuration;
  protected $twig;
  protected $logger;

  /**
   * Default construct.
   *
   * Load koba configuration.
   */
  public function __construct(ConfigFactoryInterface $configFactory, \Twig_Environment $twig, LoggerInterface $logger) {
    $this->configuration = $configFactory->get('grafisk_service_order.settings')->get('harvest')['api'];
    $this->twig = $twig;
    $this->logger = $logger;
  }

  private $api;

  /**
   * Get an instance of the HarvestAPI.
   *
   * @return \Harvest\HarvestAPI
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
   * @param \Drupal\Core\Entity\EntityInterface $order
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
      $startsOn = NULL;
    }

    $project = new Project();
    $project->set('name', $order->getTitle() . ' (#' . $order->id() . ')');
    $project->set('client_id', $clientId);
    $project->set('active', TRUE);
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

    $project->set('id', $result->data);
    $projectUrl = $this->getProjectUrl($project);

    $this->logger->info('HarvestApiProxy.createProject: @clientId @projectId', ['@clientId' => $clientId, '@projectId' => $project->id]);

    // Rename uploaded files to include Harvest project id in file name.
    if ($order->field_gs_files) {
      foreach ($order->field_gs_files as $file) {
        $filename = $file->entity->getFileUri();
        $newFilename = preg_replace('@/([^/]+)@', '/' . $project->id . '-' . '\1', $filename);
        $result = file_move($file->entity, $newFilename);
        if ($result) {
          $file->entity = $result;
          $this->logger->info('Uploaded file moved: @filename -> @newFilename (@xxx)', ['@filename' => $filename, '@newFilename' => $file->entity->getFilename(), '@xxx' => $newFilename]);
        }
        else {
          $this->logger->warning('Cannot move uploaded file: @filename', ['@filename' => $filename]);
        }
      }
    }

    // Update Harvest project to show updated filenames.
    $project->set('notes', $this->getProjectData($order));
    $result = $this->getApi()->updateProject($project);

    return [
      'projectId' => $project->id,
      'projectUrl' => $projectUrl,
    ];
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
    $number = $this->getEan($order);
    if (!$number) {
      $number = $this->getDebtor($order);
    }

    $clientName = $department . ' ' . $number;

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

    /**
     * Client names are case-insensitive in Harvest.
     */
    $existingClient = NULL;
    foreach ($this->clients as $name => $client) {
      $encoding = mb_internal_encoding();
      if (strcmp(mb_strtoupper($clientName, $encoding), mb_strtoupper($name, $encoding))) {
        $existingClient = $client;
        $clientName = $existingClient->get('name');
        break;
      }
    }

    $client = $existingClient ?: new Client();

    $client->set('name', $clientName);
    $client->set('details', $this->getClientData($order));

    if ($client->id) {
      $result = $api->updateClient($client);
      if (!$result->isSuccess()) {
        $this->logger->error($result->data);
        throw new \Exception('Cannot update client: '.$clientName);
      }

      return $client->id;
    }
    else {
      $result = $api->createClient($client);
      if (!$result->isSuccess()) {
        $this->logger->error($result->data);
        throw new \Exception('Cannot create client: '.$clientName);
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
    $data = [
      'ean' => $this->getEan($order),
      'debtor' => $this->getDebtor($order),
      'order' => $order,
    ];

    return $this->render('harvest-client-data.txt.twig', $data);
  }

  /**
   * Get debtor for an order.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   *   The order.
   *
   * @return string
   *   The debtor.
   */
  private function getDebtor(EntityInterface $order) {
    return $order->field_gs_marketing_account->value ? '4302 – Markedsføringskonto' : $order->field_gs_debtor->value;
  }

  /**
   * Get ean for an order.
   *
   * @param \Drupal\Core\Entity\EntityInterface $order
   *   The order.
   *
   * @return string
   *   The ean.
   */
  private function getEan(EntityInterface $order) {
    return $order->field_gs_marketing_account->value ? NULL : $order->field_gs_ean->value;
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
    $nodeUrl = Url::fromRoute('entity.node.canonical', ['node' => $order->id(), 'uuid' => $order->uuid()])->toString();
    $nodeUrl = Url::fromRoute('user.login', ['destination' => $nodeUrl], ['absolute' => TRUE])->toString();
    $fileUrls = [];
    if ($order->field_gs_files) {
      foreach ($order->field_gs_files as $file) {
        $fileUrls[] = $file->entity->url();
      }
    }

    $data = [
      'url' => $nodeUrl,
      'ean' => $this->getEan($order),
      'debtor' => $this->getDebtor($order),
      'order' => $order,
      'file_urls' => $fileUrls,
    ];

    return $this->render('harvest-project-data.txt.twig', $data);
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
   * @param \Harvest\Model\Project $project
   *   The Harvest project.
   *
   * @return string
   *   The project url.
   */
  public function getProjectUrl(Project $project) {
    return 'https://' . $this->configuration['account'] . '.harvestapp.com/projects/' . $project->id;
  }

  /**
   *
   */
  private function render($templateName, $data) {
    $templatePath = DRUPAL_ROOT . '/' . drupal_get_path('module', 'grafisk_service_order') . '/templates/harvest/' . $templateName;
    $template = file_get_contents($templatePath);
    $content = $this->twig->createTemplate($template)->render($data);

    return $content;
  }

  /**
   * Get all projects updated since a given time.
   *
   * @param \DateTime $updatedSince
   *
   * @return \Harvest\Model\Result
   */
  public function getProjects(\DateTime $updatedSince) {
    $api = $this->getApi();
    $d = clone $updatedSince;
    $d->setTimezone(new \DateTimeZone('UTC'));
    $result = $api->getProjects($d->format(\DateTime::ISO8601));

    return $result->isSuccess() ? $result->data : NULL;
  }

  /**
   * Get all invoices updated since a given time.
   *
   * @param \DateTime $updatedSince
   *
   * @return \Harvest\Model\Result
   */
  public function getInvoices(\DateTime $updatedSince) {
    $api = $this->getApi();
    $d = clone $updatedSince;
    $d->setTimezone(new \DateTimeZone('UTC'));
    $filter = new Filter();
    $filter->set('updated_since', $d->format(\DateTime::ISO8601));
    $result = $api->getInvoices($filter);

    return $result->isSuccess() ? $result->data : NULL;
  }

}
