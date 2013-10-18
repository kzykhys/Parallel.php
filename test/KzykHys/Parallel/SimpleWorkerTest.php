<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

class SimpleWorkerTest extends PHPUnit_Framework_TestCase
{

    public function testWorker()
    {
        $worker = new \KzykHys\Parallel\SimpleWorker(function () {
            return 10;
        });

        $this->assertEquals(10, $worker->run());
    }

    public function testArguments()
    {
        $worker = new \KzykHys\Parallel\SimpleWorker(function ($a, $b) {
            $this->assertEquals('a', $a);
            $this->assertEquals('b', $b);

            return 10;
        }, ['a', 'b']);

        $this->assertEquals(10, $worker->run());
    }

} 