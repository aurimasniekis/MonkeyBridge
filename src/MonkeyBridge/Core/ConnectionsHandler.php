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


class ConnectionsHandler
{
    /**
     * @var AbstractDriver[]
     */
    protected $drivers;

    public function readDrivers()
    {
        $data = array();
        if (count($this->drivers) > 0) {
            foreach ($this->drivers as $driver) {
                $read = $driver->read();
                if ($read) {
                    $data[] = $read;
                }
            }
        }

        return $data;
    }

    public function addDriver($driver)
    {
        if (!in_array($driver, $this->drivers)) {
            $this->drivers[] = $driver;
        }
    }
}
