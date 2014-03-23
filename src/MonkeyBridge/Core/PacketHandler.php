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

/**
 * Class PacketHandler
 *
 * @author  Aurimas Niekis <aurimas.niekis@gmail.com>
 *
 * @package MonkeyBridge\Core
 */
class PacketHandler
{
    protected $packetHandlers = array();

    public function handlePacket($packet)
    {
        $packetType = join('', array_splice(explode('\\', get_class($packet)), -1));

        if (isset($this->packetHandlers[$packetType])) {
            call_user_func($this->packetHandlers[$packetType], $packet, $this);
        }

        return $packet;
    }

    /**
     * @param $packetType
     * @param $handler
     */
    public function addHandler($packetType, $handler)
    {
        $this->packetHandlers[$packetType] = $handler;
    }

    /**
     * @param $packetType
     */
    public function removeHandler($packetType)
    {
        if (isset($this->packetHandlers[$packetType])) {
            unset($this->packetHandlers[$packetType]);
        }
    }
}
