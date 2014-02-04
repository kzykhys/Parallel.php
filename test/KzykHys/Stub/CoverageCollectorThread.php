<?php

use KzykHys\Parallel\SharedThread;

class CoverageCollectorThread extends SharedThread
{

    public function start()
    {
        $this->fork();

        $coverage = new PHP_CodeCoverage();
        $coverage->start('child_process');

        register_shutdown_function(array($this, 'shutdown'), $coverage);

        $this->waitChild();
    }

    public function shutdown(PHP_CodeCoverage $coverage)
    {
        $coverage->stop();
        $writer = new PHP_CodeCoverage_Report_PHP();
        $writer->process($coverage, __DIR__ . '/../../../build/logs/clover-child.cov');
    }

} 