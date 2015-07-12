<?php

namespace Adirelle\React\EventPromise\Tests;

use Adirelle\React\EventPromise\EventPromise;
use Evenement\EventEmitter;
use PHPUnit_Framework_TestCase;

/**
 * Description of EventPromiseTest
 *
 * @author Adirelle <adirelle+github@gmail.com>
 */
class EventPromiseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getOutcomeArguments
     */
    public function testListenOnEvent($thenMethod, $event, $unlisten = true)
    {
        $emitter = new EventEmitter();

        $called = false;
        $callback = function ($data) use (&$called) {
            $this->assertEquals(['payload'], $data);
            $called = true;
        };

        EventPromise::listen($emitter, ['resolve'], ['reject'], ['notify'])
            ->$thenMethod($callback)
            ->done();

        $emitter->emit($event, ['payload']);

        $this->assertTrue($called);

        if ($unlisten) {
            $this->assertEmpty($emitter->listeners('resolve'));
            $this->assertEmpty($emitter->listeners('reject'));
            $this->assertEmpty($emitter->listeners('notify'));
        }
    }

    /**
     * @dataProvider getOutcomeArguments
     */
    public function testIgnoreOtherEvents($thenMethod, $event)
    {
        $emitter = new EventEmitter();

        EventPromise::listen($emitter, ['resolve'], ['reject'], ['notify'])
            ->$thenMethod(function () use ($event) {
                $this->fail("Event '$event' should be ignored.");
            })
            ->done();

        $emitter->emit('otherEvent', ['payload']);
    }

    public function getOutcomeArguments()
    {
        return [
            'resolve' => ['then',      'resolve'],
            'reject'  => ['otherwise', 'reject'],
            'notify'  => ['progress',  'notify', false],
        ];
    }

    public function testCancelRemoveListeners()
    {
        $emitter = new EventEmitter();

        EventPromise::listen($emitter, ['resolve'], ['reject'], ['notify'])->cancel();

        $this->assertEmpty($emitter->listeners('resolve'));
        $this->assertEmpty($emitter->listeners('reject'));
        $this->assertEmpty($emitter->listeners('notify'));
    }

    public function testDoNoRemoveOtherListeners()
    {
        $emitter = new EventEmitter();

        $listener = function () {};
        $emitter->on('end', $listener);

        EventPromise::listen($emitter, ['end'])->done();

        $emitter->emit('end', ['payload']);

        $this->assertSame([$listener], $emitter->listeners('end'));
    }
}
