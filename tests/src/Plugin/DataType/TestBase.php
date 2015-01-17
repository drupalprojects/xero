<?php
/**
 * @file
 * Provides \DrupalTests\xero\Plugin\DataType\TestBase.
 */

namespace Drupal\Tests\xero\Plugin\DataType;

use Drupal\Tests\UnitTestCase;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\Plugin\DataType\String;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Assert setting and getting Link properties.
 */
abstract class TestBase extends UnitTestCase {

  const XERO_TYPE = FALSE;
  const XERO_TYPE_CLASS = FALSE;
  const XERO_DEFINITION_CLASS = FALSE;

  protected $typedDataManager;
  protected $dataDefinition;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    // Typed Data Manager setup.
    $this->typedDataManager = $this->getMockBuilder('\Drupal\Core\TypedData\TypedDataManager')
      ->disableOriginalConstructor()
      ->getMock();
    $this->typedDataManager->expects($this->any())
      ->method('getDefinition')
      ->with(static::XERO_TYPE)
      ->will($this->returnValue(['class' => static::XERO_DEFINITION_CLASS]));
    $this->typedDataManager->expects($this->any())
      ->method('getDefaultConstraints')
      ->willReturn([]);

    // Validation constraint manager setup.
    $validation_constraint_manager = $this->getMockBuilder('\Drupal\Core\Validation\ConstraintManager')
      ->disableOriginalConstructor()
      ->getMock();
    $validation_constraint_manager->expects($this->any())
      ->method('create')
      ->willReturn([]);
    $this->typedDataManager->expects($this->any())
      ->method('getValidationConstraintManager')
      ->willReturn($validation_constraint_manager);

    // Mock the container.
    $container = new ContainerBuilder();
    $container->set('typed_data_manager', $this->typedDataManager);
    \Drupal::setContainer($container);

    // Create data definition
    $definition_class = static::XERO_DEFINITION_CLASS;
    $this->dataDefinition = $definition_class::create(static::XERO_TYPE);
  }

}
