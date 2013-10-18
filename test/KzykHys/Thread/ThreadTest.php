<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

require __DIR__ . '/../Stub/Task.php';
require __DIR__ . '/../Stub/ChildThread.php';

class ThreadTest extends \PHPUnit_Framework_TestCase
{

    public function testEnvironment()
    {
        $parallel = new \KzykHys\Parallel\Parallel();

        $this->assertTrue($parallel->isSupported());
    }

    public function testRunnable()
    {
        $task = new Task();
        $thread = new \KzykHys\Thread\Thread($task);
        $thread->start();
        $thread->wait();
        $this->assertGreaterThan(0, $thread->getPid());
    }

    public function testSubclass()
    {
        $task = new ChildThread();
        $thread = new \KzykHys\Thread\Thread($task);
        $thread->start();
        $thread->wait();
        $this->assertGreaterThan(0, $thread->getPid());
    }

} 