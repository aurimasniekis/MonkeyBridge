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


class Bridge
{
    /**
     * @var AbstractDriver[]
     */
    protected $drivers = array();

    /**
     * @var PacketHandler
     */
    protected $packetHandler;

    /**
     * @var SignalHandler
     */
    protected $signalHandler;

    public function __construct()
    {
        $this->packetHandler = new PacketHandler();
        $this->signalHandler = new SignalHandler();
    }
}
