<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

namespace KzykHys\Parallel;

use KzykHys\Thread\Runnable;

/**
 * Simple implementation for thread
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class SimpleWorker implements Runnable
{

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @param callable $callable
     * @param array    $args
     */
    public function __construct(callable $callable, array $args = [])
    {
        $this->callable = $callable;
        $this->args     = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return call_user_func_array($this->callable, $this->args);
    }

}