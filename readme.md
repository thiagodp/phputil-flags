# phputil/flags

[![Version](https://poser.pugx.org/phputil/flags/v?style=flat-square)](https://packagist.org/packages/phputil/flags)
![Build](https://github.com/thiagodp/phputil-flags/actions/workflows/ci.yml/badge.svg?style=flat)
[![License](https://poser.pugx.org/phputil/phputil-flags/license?style=flat-square)](https://packagist.org/packages/phputil/flags)
[![PHP](http://poser.pugx.org/phputil/flags/require/php)](https://packagist.org/packages/phputil/flags)


> 🚩 A lightweight, customizable [feature flags](https://en.wikipedia.org/wiki/Feature_toggle) framework for PHP

You can customize:
- 🧠 How flags are evaluated, through [Strategies](#strategies).
- 💾 How flags are stored, through [Storages](#storages).
- 📢 Who is notified about flag changes or removal, through [Listeners](#listeners).


## Installation

> PHP 7.4 or later. No external dependencies.

```bash
composer require phputil/flags
```
👉 You may also like to install some of the official [extensions](#extensions).


## Extensions

Official extensions:

- ⏳ SOON - [`phputil/flags-pdo`](https://github.com/thiagodp/phputil-flags-pdo) - a [PDO](https://www.php.net/manual/en/intro.pdo.php)-based [storage](#storages).
- ⏳ SOON - [`phputil/flags-firebase`](https://github.com/thiagodp/phputil-flags-firebase) - a [Firebase](https://firebase.google.com/)-based [storage](#storages).
- ⏳ SOON - [`phputil/flags-webhooks`](https://github.com/thiagodp/phputil-flags-webhooks) a [listener](#listeners) that works like a webhook, by notifying external APIs about flags' changes.

Third-party extensions:
- Create yours and open an Issue to be evaluated. It may appear here.


## Usage

Basic flag checking:

```php
require_once 'vendor/autoload.php';
use phputil\flags\FlagManager;

// By default, it uses a storage-based strategy with an in-memory storage
$flag = new FlagManager();

if ( $flag->isEnabled( 'my-cool-feature' ) ) {
    echo 'Cool feature available!', PHP_EOL;
} else {
    echo 'Not-so-cool feature here', PHP_EOL;
}
```

Customizing a certain verification:
```php
// ...
use phputil\flags\FlagVerificationStrategy;

$flag = new FlagManager();

$myLuckBasedStrategy = new class implements FlagVerificationStrategy {
    function isEnabled( string $flag ): bool {
        return rand( 1, 100 ) >= 50; // 50% chance
    }
};

if ( $flag->isEnabled( 'my-cool-feature', [ $myLuckBasedStrategy ] ) ) {
    echo 'Cool feature available!', PHP_EOL;
} else {
    echo 'Not-so-cool feature here', PHP_EOL;
}
```

Customizing all the verifications:

```php
$flag = new FlagManager( null, [ $myLuckBasedStrategy ] );

if ( $flag->isEnabled( 'my-cool-feature' ) ) {
    ...
```

Setting a flag:
```php
$flag->enable( 'my-cool-feature' );
$flag->disable( 'my-cool-feature' );
$flag->setEnable( 'my-cool-feature', true /* or false */ );
```

Removing a flag:
```php
$flag->remove( 'my-cool-feature' );
```

Retrieving flag data:
```php
$flagData = $flag->getStorage()->get( 'my-cool-feature' ); // null if not found
```

Adding a listener:
```php
// ...
use phputil\flags\FlagListener;
use phputil\flags\FlagData;

$myListener = new class implements FlagListener {
  public function notify( string $event, FlagData $flagData ): void {
    if ( $event === 'change' ) {
        echo 'Flag ', $flagData->key, ' is now ', $flagData->enabled ? 'enabled': 'disabled', PHP_EOL;
    } else if ( $event === 'removal' ) {
        echo 'Flag ', $flagData->key, ' was removed.', PHP_EOL;
    }
  }
};

$flag->getListeners()->add( $myListener );

$flag->enable( 'my-cool-feature' ); // Notify the listener
```

## Customization


### Storages

Use a different flag storage by:
- Creating your own, extending [`FlagStorage`](/src/FlagStorage.php);
- Using an external [storage extension](#extensions).

How to configure it:

```php
$storage = /* Create your storage here, e.g. new InMemoryStorage() */;
$flag = new FlagManager( $storage );
```

Storages available in the framework:
- [`InMemoryStorage`](src/storages/InMemoryStorage.php), that store flags in memory.

### Strategies

Use a flag verification strategy by:
- Creating your own, extending [`FlagVerificationStrategy`](/src/FlagVerificationStrategy.php);
- Using an external [strategy extension](#extensions).

How to configure it globally:

```php
$strategies = [ /* pass your strategies here   */ ];
$flag = new FlagManager( null, $strategies );
```

Strategies available in the framework:
- [`StorageBasedVerificationStrategy`](src/strategies/StorageBasedVerificationStrategy.php), that checks flags in a storage.

👉 A flag is considered enabled when **all** the strategies considered it enabled.


### Listeners

Define a listener by:
- Creating your own, extending [`FlagListener`](/src/FlagListener.php);
- Using an external [listener extension](#extensions).

How to configure it:

```php
$flag->getListeners()->add( /* pass your listener here */ );
```


## Roadmap

- [x] Extensible library
- [ ] Official extensions:
  - [ ] PDO-based storage
  - [ ] Firebase-based storage
  - [ ] Webhook-like listener
- [ ] REST API (external repository)
- [ ] Web-based control panel (external repository)


## License

[MIT](/LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)
