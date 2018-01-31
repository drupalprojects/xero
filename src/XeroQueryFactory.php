<?php

namespace Drupal\xero;

use Drupal\Core\TypedData\TypedDataManagerInterface;
use Radcliffe\Xero\XeroClient;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\Serializer\Serializer;

class XeroQueryFactory {

  /**
   * Factory method.
   *
   * @param \Radcliffe\Xero\XeroClient $client
   *   The xero.client service.
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The serializer service.
   * @param \Drupal\Core\TypedData\TypedDataManagerInterface $typedDataManager
   *   The typed data manager service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger channel factory service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend to use.
   *
   * @return \Drupal\xero\XeroQuery
   *   Xero Query instance.
   */
  public function get(XeroClient $client, Serializer $serializer, TypedDataManagerInterface $typedDataManager, LoggerChannelFactoryInterface $loggerChannelFactory, CacheBackendInterface $cacheBackend) {
    return new XeroQuery($client, $serializer, $typedDataManager, $loggerChannelFactory, $cacheBackend);
  }

}
