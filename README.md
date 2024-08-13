# TestMonitor Asana Client

[![Latest Stable Version](https://poser.pugx.org/testmonitor/asana-client/v/stable)](https://packagist.org/packages/testmonitor/asana-client)
[![CircleCI](https://img.shields.io/circleci/project/github/testmonitor/asana-client.svg)](https://circleci.com/gh/testmonitor/asana-client)
[![StyleCI](https://styleci.io/repos/223037397/shield)](https://styleci.io/repos/223037397)
[![codecov](https://codecov.io/gh/testmonitor/asana-client/graph/badge.svg?token=MNI8M45RN1)](https://codecov.io/gh/testmonitor/asana-client)
[![License](https://poser.pugx.org/testmonitor/asana-client/license)](https://packagist.org/packages/testmonitor/asana-client)

This package provides a very basic, convenient, and unified wrapper for the [Official Asana PHP client library](https://github.com/Asana/php-asana).

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Examples](#examples)
- [Tests](#tests)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

To install the client you need to require the package using composer:

	$ composer require testmonitor/asana-client

Use composer's autoload:

```php
require __DIR__.'/../vendor/autoload.php';
```

You're all set up now!

## Usage

This client only supports **oAuth authentication**. You'll need an Asana application to proceed. If you haven't done so,
please read up with the [Asana authentication docs](https://developers.asana.com/docs/#authentication-basics) on how
to create an application.

When your Asana application is up and running, start with the oAuth authorization:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$asana = new \TestMonitor\Asana\Client($oauth);

header('Location: ' . $asana->authorizationUrl('state'));
exit();
```

This will redirect the user to a page asking confirmation for your app getting access to Asana. Make sure your redirectUrl points
back to your app. This URL should point to the following code:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$asana = new \TestMonitor\Asana\Client($oauth);

$token = $asana->fetchToken($_REQUEST['code']);
```

When everything went ok, you should have an access token (available through Token object). It will be valid for **one hour**.
After that, you'll have to refresh the token to regain access:

```php
$oauth = ['clientId' => '12345', 'clientSecret' => 'abcdef', 'redirectUrl' => 'https://redirect.myapp.com/'];
$token = new \TestMonitor\Asana\Token('eyJ0...', '0/34ccc...', 1574600877); // the token you got last time

$asana = new \TestMonitor\Asana\Client($oauth, $token);

if ($token->expired()) {
    $newToken = $asana->refreshToken();
}
```

The new token will be valid again for the next hour.

## Examples

Get a list of Asana workspaces:

```php
$workspaces = $asana->workspaces();
```

Or creating a task, for example (using a example project with gid 12345):

```php
$task = $asana->createTask(new \TestMonitor\Asana\Resources\Task([
    'completed' => false,
    'name' => 'Name of the task',
    'notes' => 'Some notes',
]), '12345');
```

## Tests

The package contains integration tests. You can run them using PHPUnit.

    $ vendor/bin/phpunit

## Changelog

Refer to [CHANGELOG](CHANGELOG.md) for more information.

## Contributing

Refer to [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

## Credits

* **Thijs Kok** - *Lead developer* - [ThijsKok](https://github.com/thijskok)
* **Stephan Grootveld** - *Developer* - [Stefanius](https://github.com/stefanius)
* **Frank Keulen** - *Developer* - [FrankIsGek](https://github.com/frankisgek)
* **Muriel Nooder** - *Developer* - [ThaNoodle](https://github.com/thanoodle)

## License

The MIT License (MIT). Refer to the [License](LICENSE.md) for more information.
