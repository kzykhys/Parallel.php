<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

require __DIR__ . '/../Stub/CoverageCollector.php';

class ParallelTest extends PHPUnit_Framework_TestCase
{

    public function testRun()
    {
        $parallel = new \KzykHys\Parallel\Parallel();
        $parallel->run([
            function () {},
            function () {}
        ]);
    }

    public function testValues()
    {
        $parallel = new \KzykHys\Parallel\Parallel();
        $values = $parallel->values([
            function () { return 'item'; },
            'foo' => function () { return new \DateTime('2013-01-01'); }
        ]);

        $this->assertEquals('item', $values[0]);
        $this->assertInstanceOf('DateTime', $values['foo']);
    }

    public function testEach()
    {
        $parallel = new \KzykHys\Parallel\Parallel();
        $parallel->each([1, 2, 3], function ($value) {});
    }

    public function testMap()
    {
        $parallel = new \KzykHys\Parallel\Parallel();
        $values = $parallel->map([1, 2, 3], function ($value) {
            return $value * 2;
        });

        $this->assertEquals([2, 4, 6], $values);
    }

    public function testCoverageOnChildProcess()
    {
        $thread = new \KzykHys\Thread\Thread(new CoverageCollector());
        $thread->start();
        $thread->wait();
    }

} 