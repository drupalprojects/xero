<?php
/**
 * @file
 * Provides \Drupal\xero\Plugin\DataType\XeroTypeBase.
 */

namespace Drupal\xero\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;
use Drupal\xero\TypedData\XeroTypeInterface;

/**
 * Xero base type for all complex xero types to inherit that need to have
 * GUID and Plural properties.
 */
abstract class XeroTypeBase extends Map implements XeroTypeInterface {

  static public $guid_name;
  static public $xero_name;
  static public $plural_name;
  static public $label;

  /**
   * {@inheritdoc}
   */
  public function getGUIDName() {
    return static::$guid_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluralName() {
    return static::$plural_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabelName() {
    return static::$label;
  }

  /**
   * {@inheritdoc}
   */
  public function view() {

    $className = substr($this->getName(), 5);

    $item = [
      '#theme' => $this->getName(),
      '#item' => $this->getValue(),
      '#attributes' => [
        'class' => ['xero-item', 'xero-item--' . $className],
      ],
    ];

    return $item;
  }

}
