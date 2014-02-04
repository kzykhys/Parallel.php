<?php
/**
 * This file is part of Parallel project.
 *
 * (C) 2013 Kazuyuki Hayashi
 */

namespace KzykHys\Parallel;

/**
 * IPC using Unix domain sockets
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Server
{

    /**
     * @var Server
     */
    private static $instance;

    /**
     * @var string
     */
    private $file;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @var bool
     */
    private $listening = false;

    /**
     * @return Server
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Singleton
     */
    private function __construct()
    {
    }

    /**
     * Create Unix domain server socket
     *
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function listen()
    {
        if ($this->listening) {
            // @codeCoverageIgnoreStart
            throw new \LogicException('Server is already listening');
            // @codeCoverageIgnoreEnd
        }

        $this->file = sys_get_temp_dir() . '/parallel' . posix_getpid() . '.sock';
        $address    = 'unix://' . $this->file;

        if (($this->socket = stream_socket_server($address)) === false) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('Failed to open unix socket: ' . $address);
            // @codeCoverageIgnoreEnd
        }

        stream_set_blocking($this->socket, 0);

        $this->listening = true;
    }

    /**
     * Accept a connection
     *
     * @return bool|mixed
     */
    public function accept()
    {
        if (($client = stream_socket_accept($this->socket)) === false) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        $data = '';

        while (($buf = stream_socket_recvfrom($client, 1024)) !== '') {
            $data .= $buf;
        }

        fclose($client);

        return unserialize($data);
    }

    /**
     * Close the socket
     */
    public function close()
    {
        fclose($this->socket);
        unlink($this->file);
        $this->listening = false;
    }

} 