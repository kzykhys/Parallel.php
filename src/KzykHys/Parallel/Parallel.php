<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

namespace KzykHys\Parallel;

use KzykHys\Thread\Thread;

/**
 * Simple multitasking library
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Parallel
{

    /**
     * Run multiple tasks asynchronously
     *
     * @param callable[] $workers
     */
    public function run($workers)
    {
        $threads = array_map(function (callable $worker) {
            return new Thread(new SimpleWorker($worker));
        }, $workers);

        $this->start($threads);
    }

    /**
     * @param callable[] $workers
     *
     * @return array
     */
    public function values($workers)
    {
        Server::getInstance()->listen();

        $threads = array_map(function (callable $worker) {
            return new SharedThread(new SimpleWorker($worker));
        }, $workers);

        $result = $this->fetch($threads);
        Server::getInstance()->close();

        return $result;
    }

    /**
     * Process all values by callback asynchronously
     *
     * @param  array|\Traversable $values
     * @param callable            $worker
     */
    public function each($values, callable $worker)
    {
        $threads = array_map(function ($value) use (&$worker) {
            return new Thread(new SimpleWorker($worker, [$value]));
        }, $values);

        $this->start($threads);
    }

    /**
     * @param array|\Traversable $values
     * @param callable $worker
     *
     * @return array
     */
    public function map($values, callable $worker)
    {
        Server::getInstance()->listen();

        $threads = array_map(function ($value) use (&$worker) {
            return new SharedThread(new SimpleWorker($worker, [$value]));
        }, $values);

        $result = $this->fetch($threads);
        Server::getInstance()->close();

        return $result;
    }

    /**
     * Returns true if your php environment support Parallel
     *
     * @return bool
     */
    public function isSupported()
    {
        if (!function_exists('pcntl_fork')) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if (!in_array(substr(PHP_SAPI, 0, 3), ['cgi', 'cli'])) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }

    /**
     * @param Thread[] $threads
     */
    protected function start(array $threads)
    {
        foreach ($threads as $thread) {
            $thread->start();
        }

        foreach ($threads as $thread) {
            $thread->wait();
        }
    }

    /**
     * @param Thread[] $threads
     * @return array
     */
    protected function fetch(array $threads)
    {
        $count   = count($threads);
        $results = [];
        $result  = [];

        foreach ($threads as $thread) {
            $thread->start();
        }

        while ($count) {
            if ($data = Server::getInstance()->accept()) {
                $results[$data[0]] = $data[1];
                $count--;
            }
        }

        foreach ($threads as $thread) {
            $thread->wait();
        }

        foreach ($threads as $key => $thread) {
            $result[$key] = $results[$thread->getPid()];
        }

        return $result;
    }

} 