<?php
/*
 * This file is part of the MonkeyBridge package.
 *
 * (c) Aurimas Niekis <aurimas.niekis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MonkeyBridge\Core;


use MonkeyBridge\Packet\PingPacket;

class Connection
{
    /**
     * @var int
     */
    protected $pid;

    /**
     * @var int
     */
    protected $masterPid;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var bool
     */
    protected $isMaster;

    /**
     * @var AbstractDriver
     */
    protected $driver;

    /**
     * @var Bridge
     */
    protected $bridge;

    /**
     * @param AbstractDriver $driver
     * @param Bridge         $bridge
     */
    public function __construct(AbstractDriver $driver, Bridge $bridge)
    {
        $this->bridge = $bridge;
        $this->uuid = md5(spl_object_hash($this));
        $this->driver = $driver;
        $this->driver->create();
    }

    public function __destruct()
    {
        $this->driver->destroy();
    }

    public function changeMode($mode, $pid = null)
    {
        $this->isMaster = $mode;
        $this->driver->changeMode($mode);

        if (!$mode) {
            $this->pid = $pid;
        } else {
            $this->setPid(posix_getpid());
            $this->setMasterPid(posix_getppid());
            $this->ping();
        }
    }

    public function send($packet)
    {
        $this->driver->write($packet);
    }

    public function receive()
    {
        return $this->driver->read();
    }

    public function ping()
    {
        $ping = new PingPacket();
        $ping->pid = $this->getPid();
        $ping->uuid = $this->getUUID();

        $this->send($ping);
    }

    /**
     * @param int $masterPid
     */
    public function setMasterPid($masterPid)
    {
        $this->masterPid = $masterPid;
    }

    /**
     * @return int
     */
    public function getMasterPid()
    {
        return $this->masterPid;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }
}
