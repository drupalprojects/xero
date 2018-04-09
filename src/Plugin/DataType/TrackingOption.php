<?php
/**
 * @file
 * Provides \Drupal\xero\Plugin\DataType\TrackingOption.
 */

namespace Drupal\xero\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Xero Tracking Option type, as defined at
 * https://developer.xero.com/documentation/api/banktransactions
 *
 * @DataType(
 *   id = "xero_tracking_option",
 *   label = @Translation("Xero Tracking Option"),
 *   definition_class = "\Drupal\xero\TypedData\Definition\TrackingOptionDefinition"
 * )
 */
class TrackingOption extends Map {}
