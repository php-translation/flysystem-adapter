# Adapter for Flysystem

[![Latest Version](https://img.shields.io/github/release/php-translation/flysystem-adapter.svg?style=flat-square)](https://github.com/php-translation/flysystem-adapter/releases)
[![Build Status](https://img.shields.io/travis/php-translation/flysystem-adapter.svg?style=flat-square)](https://travis-ci.org/php-translation/flysystem-adapter)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/php-translation/flysystem-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-translation/flysystem-adapter)
[![Quality Score](https://img.shields.io/scrutinizer/g/php-translation/flysystem-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-translation/flysystem-adapter)
[![Total Downloads](https://img.shields.io/packagist/dt/php-translation/flysystem-adapter.svg?style=flat-square)](https://packagist.org/packages/php-translation/flysystem-adapter)

This is an PHP-translation adapter for [Flysystem](https://flysystem.thephpleague.com/).

### Install

```bash
composer require php-translation/flysystem-adapter
```

##### Symfony bundle

If you want to use the Symfony bundle you may activate it in kernel:

```
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Translation\PlatformAdapter\Flysystem\Bridge\Symfony\TranslationAdapterFlysystemBundle(),
    );
}
```

Configure Flysystem adapters like
``` yaml
# /app/config/config.yml
translation_adapter_flysystem:
  filesystems:
    local:
      flysystem_service: 'local.service_id' 
      path: 'path/to/trans' 
    foobar:
      flysystem_service: 'foobar.service_id' 
      path: 'path/to/trans' 
```


This will produce two services named `php_translation.adapter.flysystem.local` 
and `php_translation.adapter.flysystem.foobar` that could be used in the configuration for
the [Translation Bundle](https://github.com/php-translation/symfony-bundle).

### Documentation

Read our documentation at [http://php-translation.readthedocs.io](http://php-translation.readthedocs.io/en/latest/).

### Contribute

Do you want to make a change? This repository is READ ONLY. Submit your 
pull request to [php-translation/platform-adapter](https://github.com/php-translation/platform-adapter).
