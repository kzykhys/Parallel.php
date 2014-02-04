<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

namespace KzykHys\Parallel;

use KzykHys\Thread\Thread;

/**
 * Send return value to IPC server
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class SharedThread extends Thread
{

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->fork();
        $this->waitChild();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->runnable) {
            return $this->runnable->run();
        }

        return null;
    }

    protected function waitChild()
    {
        if (!$this->pid) {
            $file    = sys_get_temp_dir() . '/parallel' . posix_getppid() . '.sock';
            $address = 'unix://' . $file;
            $result  = $this->run();

            if ($client = stream_socket_client($address)) {
                stream_socket_sendto($client, serialize([posix_getpid(), $result]));
                fclose($client);
            }

            exit;
        }
    }

} 