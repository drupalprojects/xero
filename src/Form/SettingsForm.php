<?php
/**
 * @file
 * Contains \Drupal\xero\Form\SettingsForm.
 */

namespace Drupal\xero\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Serializer;
use Drupal\xero\XeroQuery;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Provide configuration form for user to provide Xero API information for a
 * private application.
 */
class SettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  protected $query = FALSE;

  /**
   * Inject dependencies into the form except for XeroClient because we want to
   * handle errors properly instead of failing and exploding spectacularly.
   *
   * @param $config_factory
   *   Configuration factory interface.
   * @param $query
   *   An instance of XeroClient or NULL if it fails, which is most likely the
   *   case on first load.
   * @param $serializer
   *   Serializer object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, $query, Serializer $serializer, LoggerChannelFactoryInterface $logger_factory) {
    $this->setConfigFactory($config_factory);
    $this->query = $query;
    $this->serializer = $serializer;
    $this->logger = $logger_factory->get('xero');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xero_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the configuration from ConfigFormBase::config().
    $config = self::config('xero.settings');

    // Open the fieldset if there was a problem loading the library.
    $form['oauth'] = array(
      '#type' => 'fieldset',
      '#title' => t('Xero Configuration'),
      '#collapsible' => TRUE,
      '#collapsed' => $this->query->client !== FALSE,
      '#tree' => TRUE,
    );

    $form['oauth']['consumer_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Xero Consumer Key'),
      '#description' => t('Provide the consumer key for your private application on xero.'),
      '#default_value' => $config->get('oauth.consumer_key'),
      '#required' => TRUE,
    );

    $form['oauth']['consumer_secret'] = array(
      '#type' => 'textfield',
      '#title' => t('Xero Consumer Secret'),
      '#description' => t('Provide the consumer secret for your private application on xero.'),
      '#default_value' => $config->get('oauth.consumer_secret'),
      '#required' => TRUE,
    );

    $form['oauth']['cert_path'] = array(
      '#type' => 'textfield',
      '#title' => t('Xero Certificate Path'),
      '#description' => t('Provide the full path and file name to your Xero certificate.'),
      '#default_value' => $config->get('oauth.cert_path'),
      '#element_validate' => array(array($this, 'validateFileExists')),
      '#required' => TRUE,
    );

    $form['oauth']['key_path'] = array(
      '#type' => 'textfield',
      '#title' => t('Xero Key Path'),
      '#description' => t('Provide the full path and file name to your Xero certificate private key.'),
      '#default_value' => $config->get('oauth.key_path'),
      '#element_validate' => array(array($this, 'validateFileExists')),
      '#required' => TRUE,
    );

    $form['defaults'] = array(
      '#type' => 'fieldset',
      '#title' => t('Defaults'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#tree' => TRUE,
    );

    if ($this->query->client !== FALSE) {
      $account_options = array();

      try {
        $context = array('plugin_id' => 'xero_account');
        $accounts = $this->query
          ->setType($context['plugin_id'])
          ->setMethod('get')
          ->setFormat('xml')
          ->execute();

        foreach ($accounts as $account) {
          // Bank accounts do not have a code, exclude them.
          if ($account->get('Code')->getValue()) {
            $account_options[$account->get('Code')->getValue()] = $account->get('Name')->getValue();
          }
        }
      }
      catch (RequestException $e) {
        $this->logger->error('%message: %response', array('%message' => $e->getMessage(), '%response' => $e->getResponse()->getBody(TRUE)));
        return parent::buildForm($form, $form_state);
      }
      catch (\Exception $e) {
        $this->logger->error('%message', array('%message' => $e->getMessage()));
        return parent::buildForm($form, $form_state);
      }

      $form['defaults']['account'] = array(
        '#type' => 'select',
        '#title' => t('Default Account'),
        '#description' => t('Choose a default account.'),
        '#options' => $account_options,
        '#default_value' => $config->get('defaults.account'),
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate that certificate or key file exist.
   */
  public function validateFileExists($element, FormStateInterface $form_state) {
    if (!file_exists($element['#value'])) {
      $form_state->setError($element, t('The specified file either does not exist, or is not accessible to the web server.'));
    }
  }

  /**
   * {@inheritdoc}
   */
   public function submitForm(array &$form, FormStateInterface $form_state) {
     // Set configuration.
     $config = self::config('xero.settings');
     $form_state_values = $form_state->getValues();
     $config
       ->set('oauth.consumer_key', $form_state_values['oauth']['consumer_key'])
       ->set('oauth.consumer_secret', $form_state_values['oauth']['consumer_secret'])
       ->set('oauth.cert_path', $form_state_values['oauth']['cert_path'])
       ->set('oauth.key_path', $form_state_values['oauth']['key_path']);

     // Check for default account and save setting, if available.
     if (isset($form_state_values['defaults'])) {
       $config->set('defaults.account', $form_state_values['defaults']['account']);
     }

     $config->save();
   }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('xero.query'), $container->get('serializer'), $container->get('logger.factory'));
  }
}