<?php
/**
 * @file
 * Contains \Drupal\Tests\xero\Form\SettingsFormTest
 */

namespace Drupal\Tests\xero\Unit\Form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormState;
use Drupal\Tests\Core\Form\FormTestBase;
use Drupal\xero\Form\SettingsForm;

/**
 * Test the Xero configuration form.
 *
 * @coversDefaultClass \Drupal\xero\Form\SettingsForm
 */
class SettingsFormTest extends FormTestBase {

  protected $privateKey;
  protected $pemFile;

  /**
   * Test file element validation.
   */
  public function testNoPrivateKey() {

    $key = $this->createToken();
    $secret = $this->createToken();

    // Mock the getter.
    $this->config->expects($this->any())
      ->method('get')
      ->withConsecutive(['oauth.consumer_key'], ['oauth.consumer_secret'], ['oauth.key_path'])
      ->will($this->onConsecutiveCalls($key, $secret, ''));

    $form_state = new FormState();
    $form = $this->settingsForm->buildForm([], $form_state);

    $this->assertEquals('xero_configuration_form', $this->settingsForm->getFormId());

    // Assert that default value is correct.
    $this->assertEquals($key, $form['oauth']['consumer_key']['#default_value']);
    $this->assertEquals($secret, $form['oauth']['consumer_secret']['#default_value']);
    $this->assertEquals('', $form['oauth']['key_path']['#default_value']);

    // Set the #parents array to mock form build.
    $form['oauth']['key_path']['#parents'] = ['oauth', 'key_path'];
    $form['oauth']['key_path']['#value'] = '';

    // Change some values and test form validation.
    $form_state->setValueForElement($form['oauth']['key_path'], '');

    $this->settingsForm->validateFileExists($form['oauth']['key_path'], $form_state);
    $this->assertEquals('The specified file either does not exist, or is not accessible to the web server.', $form_state->getError($form['oauth']['key_path']));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Mock XeroQuery service.
    $this->xeroQuery = $this->getMockBuilder('\Drupal\xero\XeroQuery')
      ->disableOriginalConstructor()
      ->getMock();

    // Mock config object.
    $this->config = $this->getMockBuilder('\Drupal\Core\Config\ImmutableConfig')
      ->disableOriginalConstructor()
      ->getMock();

    // Mock ConfigFactory service.
    $configFactory = $this->getMockBuilder('Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();
    $configFactory->expects($this->any())
      ->method('getEditable')
      ->with('xero.settings')
      ->will($this->returnValue($this->config));

    // Mock Logger Channel Factory service.
    $loggerChannel = $this->getMockBuilder('Drupal\Core\Logger\LoggerChannel')
      ->disableOriginalConstructor()
      ->getMock();

    $loggerFactory = $this->getMockBuilder('Drupal\Core\Logger\LoggerChannelFactory')
      ->disableOriginalConstructor()
      ->getMock();
    $loggerFactory->expects($this->any())
      ->method('get')
      ->with('xero')
      ->willReturn($loggerChannel);

    $stringTranslation = $this->getMockBuilder('Drupal\Core\StringTranslation\TranslationManager')
      ->disableOriginalConstructor()
      ->getMock();
    $stringTranslation->expects($this->any())
      ->method('translate')
      ->willReturn('The specified file either does not exist, or is not accessible to the web server.');

    // Mock the container.
    $container = new ContainerBuilder();
    $container->set('logger.factory', $loggerFactory);
    $container->set('config.factory', $configFactory);
    $container->set('xero.query', $this->xeroQuery);
    $container->set('string_translation', $stringTranslation);
    \Drupal::setContainer($container);

    $this->settingsForm = SettingsForm::create($container);

    // Create private key file.
    $this->pemFile = $this->getRandomGenerator()->word(15) . '.pem';
    $privateKey = $this->createPrivateKey();
    openssl_pkey_export($privateKey, $output);
    file_put_contents($this->pemFile, $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
    if (file_exists($this->pemFile)) {
      unlink($this->pemFile);
    }

    parent::tearDown();
  }

  /**
   * Create a private key for tests.
   *
   * @return openssl_pkey
   *   An OpenSSL private key object.
   *
   * @see \BlackOptic\Bundle\XeroBundle\XeroClient::__construct().
   */
  private function createPrivateKey() {
    return openssl_pkey_new(['digest_alg' => 'sha1', 'private_key_bits' => 1024, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
  }

  /**
   * Create a mock consumer key or secret.
   *
   * @return string
   *   A 30 character string that can be used as a consumer key or secret.
   */
  protected function createToken() {
    return strtoupper(hash('ripemd128', md5($this->getRandomGenerator()->string(30))));
  }

  /**
   * Create a Guid.
   *
   * @return string
   *   A valid globally-unique identifier.
   */
  protected function createGuid($braces = TRUE) {
    $hash = strtoupper(hash('ripemd128', md5($this->getRandomGenerator()->string(100))));
    $guid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4);
    $guid .= '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);

    // A Guid string representation should be output as lower case per UUIDs
    // and GUIDs Network Working Group INTERNET-DRAFT 3.3.
    $guid = strtolower($guid);

    return $guid;
  }
}
