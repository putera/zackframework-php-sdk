# ZackFramework SDK for PHP (v1)

This repository contains the open source PHP SDK that allows you to access the ZackFramework Platform from your PHP app.

## Installation

The ZackFramework PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require zackframework/php-sdk
```

## Usage

```php
<?php

require_once('./vendor/autoload.php');

use Zackframework\Zackframework;
use Zackframework\Exceptions\ZackSDKException;

$zf = new Zackframework([
	'api_key' => 'Your API Key',
	'api_secret' => 'Your API Secret',
	'api_url' => 'API Url'
]);

try
{
	$me = $zf->get('/profile/me');

	echo 'Your name is : <b>' . $me->fullname .'</b> !';
}
catch (ZackSDKException $e)
{
	die($e->getMessage());
}
```