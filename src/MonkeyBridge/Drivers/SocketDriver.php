<?php
/*
 * This file is part of the MonkeyBridge package.
 *
 * (c) Aurimas Niekis <aurimas.niekis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MonkeyBridge\Drivers;


use MonkeyBridge\Core\AbstractDriver;
use MonkeyBridge\Exception\DriverException;

class SocketDriver extends AbstractDriver
{
    /**
     * @var resource[]
     */
    public $sockets;

    /**
     * @var bool
     */
    protected $mode;

    public function create()
    {
        if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $this->sockets) === false) {
            throw new DriverException(
                posix_getpid() . " SocketDriver: socket_create_pair() failed. Reason: " .
                $this->getErrorMessage()
            );
        }
    }

    public function destroy()
    {
        if (is_resource($this->sockets[0])) {
            socket_close($this->sockets[0]);
        }

        if (is_resource($this->sockets[1])) {
            socket_close($this->sockets[1]);
        }
    }


    public function changeMode($mode)
    {
        $this->mode = $mode;
        if ($mode) {
            // This is master
            if (is_resource($this->sockets[0])) {
                socket_close($this->sockets[0]);
            }
        } else {
            // This is slave
            if (is_resource($this->sockets[1])) {
                socket_close($this->sockets[1]);
            }
        }
    }

    public function write($data)
    {
        $serialized = serialize($data);
        $header = pack('N', strlen($serialized));    // 4 byte length
        $buffer = $header . $serialized;
        $total = strlen($buffer);
        while (true) {
            $sent = socket_write($this->getSocket(), $buffer);
            if ($sent === false) {
                throw new DriverException(
                    posix_getpid() . " SocketDriver: socket_write() failed. Reason: " . $this->getErrorMessage()
                );
                break;
            }
            if ($sent >= $total) {
                break;
            }
            $total -= $sent;
            $buffer = substr($buffer, $sent);
        }
    }

    public function read()
    {
        // read 4 byte length first
        $header = '';
        do {
            $read = socket_read($this->getSocket(), 4 - strlen($header), PHP_BINARY_READ);
            if ($read === false or $read === '') {
                return null;
            }
            $header .= $read;
        } while (strlen($header) < 4);
        list($len) = array_values(unpack("N", $header));

        // read the full buffer
        $buffer = '';
        do {
            $read = socket_read($this->getSocket(), $len - strlen($buffer), PHP_BINARY_READ);
            if ($read === false or $read == '') {
                return null;
            }
            $buffer .= $read;
        } while (strlen($buffer) < $len);

        $data = unserialize($buffer);

        return $data;
    }

    public function getSocket()
    {
        if ($this->mode) {
            return $this->sockets[1];
        } else {
            return $this->sockets[0];
        }
    }

    protected function getErrorMessage($socket = null)
    {
        if ($socket) {
            $errorCode = socket_last_error($socket);
        } else {
            $errorCode = socket_last_error();
        }

        return socket_strerror($errorCode);
    }
}
