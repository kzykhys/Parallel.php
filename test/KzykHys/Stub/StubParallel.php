<?php

use KzykHys\Parallel\Parallel;
use KzykHys\Parallel\Server;
use KzykHys\Parallel\SimpleWorker;

class StubParallel extends Parallel
{

    /**
     * @param callable[] $workers
     *
     * @return array
     */
    public function values($workers)
    {
        Server::getInstance()->listen();

        $threads = array_map(function (callable $worker) {
            return new CoverageCollectorThread(new SimpleWorker($worker));
        }, $workers);

        $result = $this->fetch($threads);
        Server::getInstance()->close();

        return $result;
    }

} 