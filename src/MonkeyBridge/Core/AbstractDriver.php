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


abstract class AbstractDriver
{
    abstract public function create();

    abstract public function destroy();

    abstract public function changeMode($mode);

    abstract public function write($data);

    abstract public function read();

}
