<?php

use KzykHys\Thread\Runnable;

class CoverageCollector implements Runnable
{

    public function run()
    {
        $coverage = new PHP_CodeCoverage();
        $coverage->start('child_process');

        register_shutdown_function(array($this, 'shutdown'), $coverage);
    }

    public function shutdown(PHP_CodeCoverage $coverage)
    {
        $coverage->stop();
        $writer = new PHP_CodeCoverage_Report_PHP();
        $writer->process($coverage, __DIR__ . '/../../../build/logs/clover-child.cov');
    }

}