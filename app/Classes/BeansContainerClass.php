<?php
namespace App\Classes;
class BeansContainerClass implements BeansContainer
{
    /**
     * BEANS CONTAINER'S SIZE IN SPOONS
     * @var float
     */
    protected $beansContainerSize;

    /**
     * NUMBER OF SPOONS OF BEANS IN THE CONTAINER
     * @var integer
     */
    protected $numSpoons = 0;

    /**
     * @param  integer $beansContainerSize
     * @return void
     */
    public function __construct($beansContainerSize = 50)
    {
        $this->beansContainerSize = $beansContainerSize;
    }

    public function addBeans($numSpoons) : void
    {
        $spaceForBeans = $this->beansContainerSize - $this->numSpoons;
        if( $numSpoons > $spaceForBeans )
        {
            throw new ContainerFullException("Trying to add {$numSpoons} spoons to {$spaceForBeans} spoons space. Not enough capacity.");
        } else {
            $this->numSpoons += $numSpoons;
        }
    }

    /**
     *
     * @throws ContainerException
     * @param integer $numSpoons NUMBER OF SPOONS
     * @return integer
     */
    public function useBeans($numSpoons) : int
    {
        if ( $numSpoons > $this->numSpoons )
        {
            throw new ContainerException("Not enough beans in the container");
            $availableSpoons = $this->numSpoons;
            $this->numSpoons = 0;
            return $availableSpoons;
        }

        $this->numSpoons -= $numSpoons;
        return $numSpoons;
    }

    /**
     * RETURNS NUMBER OF SPOONS LEFT
     *
     * @return integer
     */
    public function getBeans() : int
    {
        return $this->numSpoons;
    }
}
