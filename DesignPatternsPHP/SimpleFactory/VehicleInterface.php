<?php

namespace MyLib\DesignPatterns\SimpleFactory;

/**
 * VehicleInterface is a contract for a vehicle
 */
interface VehicleInterface
{
    /**
     * @param mixed $destination
     *
     * @return mixed
     */
    public function driveTo($destination);
}
