<?php
/**
 * @file
 * Provides \Drupal\xero\Plugin\DataType\TrackingCategory.
 */

namespace Drupal\xero\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Xero Tracking Category type, as defined at
 * https://developer.xero.com/documentation/api/tracking-categories
 *
 * @DataType(
 *   id = "xero_tracking",
 *   label = @Translation("Xero Tracking Category"),
 *   definition_class = "\Drupal\xero\TypedData\Definition\TrackingCategoryDefinition"
 * )
 */
class TrackingCategory extends Map {}
