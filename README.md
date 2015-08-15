# Xero module

This module provides classes to help interface with the Xero Account SaaS product. You will need to be familiar with the [Xero API](http://developer.xero.com). For more information, see API.txt.

## Requirements

* You must have a private application created on xero.com for your organization.
  * The application public cert and private key should be available to Apache, preferrably outside of the webroot.
* The [PHP-Xero](https://github.com/mradcliffe/PHP-Xero) library should be downloaded and extracted into the include/ directory so that `include/xero.php` exists.

## Installation

* Enable Xero module.

## Configuration

* Go to `admin/config/services/xero`
* Fill out the information for your private application and click Save.

## Troubleshooting

* If you encounter an error regarding accounts on the configuration page, refresh cache and try again. By default, the application will try to load the accounts from cache to prevent unnecessary requests.

* If you encounter certificate errors, this is most likely caused by an invalid certificate such as expiration. Although the certificate will "work" the certificate should be updated.
