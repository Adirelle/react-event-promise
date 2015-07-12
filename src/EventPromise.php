<?php

namespace Adirelle\React\EventPromise;

use Evenement\EventEmitterInterface;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;

/**
 *
 */
class EventPromise
{
    /**
     *
     * @param EventEmitterInterface $emitter
     * @param string[] $fulfillEvents
     * @param string[] $rejectEvents
     * @param string[] $notifyEvents
     * @return ExtendedPromiseInterface
     */
    public static function listen(
        EventEmitterInterface $emitter,
        array $fulfillEvents = [],
        array $rejectEvents = [],
        array $notifyEvents = []
    ) {
        $listeners = [];

        $removeListeners = function () use ($emitter, &$listeners) {
            foreach ($listeners as $event => $callback) {
                $emitter->removeListener($event, $callback);
            }
            $listeners = [];
        };

        $promise = new Promise(
            function ($resolve, $reject, $notify) use ($emitter, &$listeners, $fulfillEvents, $rejectEvents, $notifyEvents) {
                $listeners = array_merge(
                    static::registerCallbacks($emitter, $fulfillEvents, $resolve),
                    static::registerCallbacks($emitter, $rejectEvents, $reject),
                    static::registerCallbacks($emitter, $notifyEvents, $notify)
                );
            },
            $removeListeners
        );

        return $promise->always($removeListeners);
    }

    /**
     *
     * @param EventEmitterInterface $emitter
     * @param string[] $events
     * @param callable $callback
     * @return callable[]
     */
    protected static function registerCallbacks(EventEmitterInterface $emitter, array $events, callable $callback)
    {
        if (empty($events)) {
            return [];
        }
        $wrappedCallback = function () use ($callback) {
            return $callback(func_get_args());
        };
        $listeners = array_fill_keys($events, $wrappedCallback);
        foreach ($listeners as $event => $callback) {
            $emitter->on($event, $callback);
        }
        return $listeners;
    }
}
