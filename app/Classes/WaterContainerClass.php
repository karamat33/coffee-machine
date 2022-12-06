<?php
namespace App\Classes;
class WaterContainerClass implements WaterContainer
{
    /**
     * CAPACITY OF WATER CONTAINER IN LITRES
     * @var float
     */
    protected $waterContainerVolume;

    /**
     * NUMBER OF LITRES IN THE CONTAINER
     * @var float
     */
    protected $litres = 0;

    /**
     * @param  float   $waterContainerVolume
     * @return void
     */
    public function __construct( $waterContainerVolume = 2 )
    {

        $this->waterContainerVolume = $waterContainerVolume;
    }

    /**
     * ADDS WATER TO THE COFFEE MACHINE'S WATER CONTAINER
     *
     * @param float $litres
     * @throws ContainerFullException
     *
     * @return void
     */
    public function addWater($litres) : void
    {
        if ( $litres > ($this->waterContainerVolume - $this->litres) )
        {
            throw new ContainerFullException("Not enough volume left in the water container");
        }
        $this->litres +=  $litres;
    }

    /**
     *
     * @throws ContainerException
     * @param float $litres
     * @return integer
     */
    public function useWater($litres) : float
    {
        if ( $litres > $this->litres )
        {
            throw new ContainerException("Not enough water in the container");
        }

        $this->litres -= $litres;
        return $litres;
    }

    /**
     * RETURN NUMBER OF LITRES LEFT
     *
     * @return float number of litres
     */
    public function getWater() : float
    {

        return $this->litres;
    }
}
