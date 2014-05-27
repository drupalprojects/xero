<?php
/**
 * @file
 * Provides \Drupal\xero\TypedData\Definition\InvoiceDefinition.
 */

namespace Drupal\xero\TypedData\Definition;

use Drupal\Core\TypeDdata\ListDataDefinition;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\ComplexDataDefinitionBase;

/**
 * Xero Invoice data definition
 */
class InvoiceDefinition extends ComplexDataDefinitionBase {
  /**
   * {@inheritdoc}
   *
   * @todo CreditNotes
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $info = &$this->propertyDefinitions;

      $type_options = array('choices' => array('ACCREC', 'ACCPAY'));
      $line_type_options = array('choices' => array('Exclusive', 'Inclusive', 'NoTax'));
      $status_options = array('choices' => array('DRAFT', 'SUBMITTED', 'DELETED', 'AUTHORISED', 'PAID', 'INVOICED'));

      // UUID is read only.
      $info['InvoiceID'] = DataDefinition::create('uuid')->setLabel('Invoice ID')->setReadOnly();

      // Writeable properties.
      $info['Type'] = DataDefinition::create('string')->setRequired()->setLabel('Type')->addConstraint('Choice', $type_options);
      $info['Contact'] = DataDefinition::create('xero_contact')->setRequired()->setLabel('Contact');
      $info['LineItems'] = ListDataDefinition::create('xero_line_item')->setRequired()->setLabel('Line Items');

      // Recommended
      $info['Date'] = DataDefinition::create('string')->setLabel('Date')->addConstraint('Date');
      $info['DueDate'] = DataDefinition::create('string')->setLabel('Due Date')->addConstraint('Date');
      $info['LineAmountTypes'] = DataDefinition::create('string')->setLabel('Line Amount Type')->addConstraint('Choice', $line_type_options);

      // Optional
      $info['InvoiceNumber'] = DataDefinition::create('string')->setLabel('Invoice #');
      $info['Reference'] = DataDefinition::create('string')->setLabel('Reference');
      $info['BrandingThemeID'] = DataDefinition::create('uuid')->setLabel('Branding Theme ID');
      $info['Url'] = DataDefinition::create('url')->setLabel('URL');
      $info['CurrencyCode'] = DataDefinition::create('string')->setLabel('Currency code');
      $info['CurrencyRate'] = DataDefinition::create('float')->setLabel('Currency rate');
      $info['Status'] = DataDefinition::create('string')->setLabel('Status')->addConstraint('Choice', $status_options);
      $info['SentToContact'] = DataDefinition::create('boolean')->setLabel('Sent?');
      $info['ExpectedPaymentDate'] = DataDefinition::create('datetime_iso8601')->setLabel('Expected Payment Date');
      $info['PlannedPaymentDate'] = DataDefinition::create('datetime_iso8601')->setLabel('Planned Payment Date');
      $info['SubTotal'] = DataDefinition::create('float')->setLabel('Sub-Total');
      $info['TotalTax'] = DataDefinition::create('float')->setLabel('Total Tax');
      $info['Total'] = DataDefinition::create('float')->setLabel('Total');

      // Read-only
      $info['HasAttachments'] = DataDefinition::create('boolean')->setLabel('Has Attachments?')->setReadOnly();
      $info['Payments'] = ListDataDefinition::create('xero_payment')->setLabel('Payments')->setReadOnly();
      $info['AmountDue'] = DataDefinition::create('float')->setLabel('Amount Due')->setReadOnly();
      $info['AmountPaid'] = DataDefinition::create('float')->setLabel('Amount Paid')->setReadOnly();
      $info['AmountCredited'] = DataDefinition::create('float')->setLabel('Amount Credited')->setReadOnly();
      $info['UpdatedDateUTC'] = DataDefinition::create('datetime_iso8601')->setLabel('Updated Date')->setReadOnly();
    }
    return $this->propertyDefinitions;
  }
}
