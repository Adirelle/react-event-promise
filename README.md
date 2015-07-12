EventPromise
============

EventPromise allows you to easily create [Promise](https://github.com/reactphp/promise)s that react
on events emitted by an [EventEmitterInterface](https://github.com/igorw/evenement).

Listeners are automatically removed when the Promise is fulfilled, rejected or cancelled.

[![Build Status](https://travis-ci.org/Adirelle/react-event-promise.svg)](https://travis-ci.org/Adirelle/react-event-promise)

Fetch
-----

The recommended way to install EventPromise is through composer.

Just create a composer.json file for your project:

```json
{
    "require": {
        "adirelle/react-event-promise": "@stable"
    }
}
```

API
---

Promises are created using the `Adirelle\React\EventPromise\EventPromise::listen` method:

```php
use \Adirelle\React\EventPromise\EventPromise;
// ...

    EventPromise::listen($someEventEmitter, ['eventToResolve'], ['eventToReject'], ['eventToNotify'])
        ->then(
            function ($arguments) {
                echo 'Event "eventToResolve" emitted';
            },
            function ($arguments) {
                echo 'Event "eventToReject" emitted';
            },
            function ($arguments) {
                echo 'Event "eventToNotify" emitted';
            }
        );
```

License
-------

EventPromise is released under the [MIT](https://github.com/Adirelle/react-event-promise/blob/master/LICENSE) license.
