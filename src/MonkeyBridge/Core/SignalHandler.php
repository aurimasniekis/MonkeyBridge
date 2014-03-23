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


class SignalHandler
{
    /**
     * @var int
     */
    protected $signal;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var array
     */
    protected $sorted = array();
    
    /**
     * @param int $signal
     */
    public function __construct($signal = SIGCHLD)
    {
        $this->signal = $signal;

        $this->attachSignalHandler();
    }

    public function attachSignalHandler()
    {
        pcntl_signal($this->signal, array($this, "handleSignal"));
    }

    /**
     * @param $pid
     */
    public function dispatch($pid)
    {
        posix_kill($pid, $this->signal);
    }

    /**
     * @param $signal
     */
    public function handleSignal($signal)
    {
        $listeners = $this->getListeners();

        foreach ($listeners as $listener) {
            call_user_func($listener, $signal, $this);
        }
    }

    public function addListener($listener, $priority = 0)
    {
        $this->listeners[$priority][] = $listener;
        unset($this->sorted);
    }

    public function getListeners()
    {
        if (empty($this->sorted)) {
            $this->sortListeners();
        }

        return $this->sorted;
    }

    protected function sortListeners()
    {
        $this->sorted = array();

        if (!empty($this->listeners)) {
            krsort($this->listeners);
            $this->sorted = call_user_func_array('array_merge', $this->listeners);
        }
    }
}
