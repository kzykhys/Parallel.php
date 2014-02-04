<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

namespace KzykHys\Thread;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Thread implements Runnable
{

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var Runnable
     */
    protected $runnable;

    /**
     * @param Runnable $runnable
     */
    public function __construct(Runnable $runnable = null)
    {
        $this->runnable = $runnable;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->runnable) {
            $this->runnable->run();
        }

        return null;
    }

    /**
     * Start a thread
     */
    public function start()
    {
        $this->fork();

        if (!$this->pid) {
            $this->run();
            exit;
        }
    }

    /**
     * Waits on a forked child
     */
    public function wait()
    {
        if ($this->pid) {
            pcntl_waitpid($this->pid, $status);
        }
    }

    /**
     * Returns process id
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Forks the currently running process
     *
     * @throws \RuntimeException
     */
    protected function fork()
    {
        if (($this->pid = pcntl_fork()) == -1) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('Unable to fork child process');
            // @codeCoverageIgnoreEnd
        }
    }

}