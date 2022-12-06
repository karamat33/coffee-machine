<?php
namespace App\Classes;

use Illuminate\Support\Facades\Redis;

class EspressoMachineClass implements EspressoMachineInterface
{

    const BEANS_USED_PER_ESPRESSO = 1;
    const LITRES_USED_PER_ESPRESSO = 0.05;

    /**
     * BEANS CONTAINER
     * @var BeansContainer
     */
    protected $beansContainer;

    /**
     * WATER CONTAINER
     * @var WaterContainer
     */
    protected $waterContainer;



    /**
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * ADDS BEANS TO THE COFFEE MACHINE'S BEANS CONTAINER
     *
     * @param integer $numSpoons number of spoons of beans
     * @throws ContainerFullException, EspressoMachineException
     *
     * @return void
     */
    public function addBeans($numSpoons)
    {
        if ( !$this->hasBeansContainer() )
        {
            throw new EspressoMachineException("The machine hasn't got a bean container");
        }
        $this->beansContainer->addBeans($numSpoons);
    }

    /**
     *
     * @throws EspressoMachineException
     * @param integer $numSpoons number of spoons of beans
     * @return integer
     */
    public function useBeans($numSpoons)
    {
        if ( !$this->hasBeansContainer() )
        {
            throw new EspressoMachineException("The machine hasn't got a bean container");
        }
        $this->beansContainer->useBeans($numSpoons);
    }

    /**
     * RETURNS NUMBER OF SPOONS OF BEANS LEFT IN THE CONTAINER
     *
     * @return integer
     */
    public function getBeans()
    {
        return $this->beansContainer->getBeans();
    }

    /**
     * ADDS WATER TO THE MACHINE'S WATER CONTAINER
     *
     * @param float $litres
     * @throws ContainerFullException, EspressoMachineException
     *
     * @return void
     */
    public function addWater($litres)
    {
        $this->waterContainer->addWater($litres);
    }

    /**
     * USE $litres FROM THE CONTAINER
     *
     * @throws EspressoMachineException
     * @param float $litres
     * @return integer
     */
    public function useWater($litres)
    {

        if ( !$this->hasWaterContainer() )
        {
            throw new EspressoMachineException("The machine hasn't got a water container");
        }
        return $this->waterContainer->useWater($litres);

    }

    /**
     * RETURN THE VOLUME OF WATER LEFT IN THE WATER CONTAINER
     *
     * @return float number of litres
     */
    public function getWater()
    {

        if ( !$this->hasWaterContainer() )
        {
            throw new EspressoMachineException("The machine hasn't got a water container");
        }

        return $this->waterContainer->getWater();
    }



    /**
     * MAKES ANY REQUESTED COFFEE
     *
     * @throws  NoBeansException, NoWaterException
     *
     * @return float of litres of coffee made
     */
    protected function makeEspressos($quantity)
    {

        try {
            $this->useBeans(self::BEANS_USED_PER_ESPRESSO * $quantity);
        }
        catch (EspressoMachineException $e)
        {
            throw new NoBeansException("Not enough beans to make an espresso. {$this->getBeans()} spoons remaining.");
        }

        try {
            $this->useWater(self::LITRES_USED_PER_ESPRESSO * $quantity);
        }
        catch (EspressoMachineException $e)
        {
            throw new NoWaterException("Not enough water to make an espresso. {$this->getWater()} litres remaining.");
        }

        $litres_of_coffee_made = self::LITRES_USED_PER_ESPRESSO * $quantity;


        $redis = Redis::connection();

        $beansContainerCapacity = $redis->get('settings:1:BEANS_CONTAINER_CAPACITY') == null ? 50 : $redis->get('settings:1:BEANS_CONTAINER_CAPACITY');
        $waterContainerCapacity = $redis->get('settings:1:WATER_CONTAINER_CAPACITY') == null ? 50 : $redis->get('settings:1:WATER_CONTAINER_CAPACITY');

        $redis->set('settings:1:BEANS_CONTAINER_CAPACITY', $beansContainerCapacity - (self::BEANS_USED_PER_ESPRESSO * $quantity));
        $redis->set('settings:1:WATER_CONTAINER_CAPACITY', $waterContainerCapacity - (self::LITRES_USED_PER_ESPRESSO * $quantity));


        return $litres_of_coffee_made;
    }

    /**
     * MAKES AN ESPRESSO
     *
     * @throws  NoBeansException, NoWaterException
     *
     * @return float OF LITRES OF COFFEE
     */
    public function makeEspresso() : float
    {
        return $this->makeEspressos(1);
    }

    /**
     * MAKES A DOUBLE ESPRESSO
     * @see makeEspresso
     * @throws  NoBeansException, NoWaterException
     *
     * @return float OF LITRES OF COFFEE
     */
    public function makeDoubleEspresso() : float
    {
        return $this->makeEspressos(2);
    }

    /**
     * THIS FUNCTION WILL DISPLAY THE STATUS OF THE MACHINE
     * IT WILL RETURN 1 OF THE FOLLOWING STATUSES:
     *
     * NEEDS MORE WATER AND BEANS
     * NEEDS MORE BEANS
     * NEEDS MORE WATER
     * {Integer} SPOON OF BEANS LEFT AND {Integer} LITRES OF WATER LEFT
     *
     * @return string
     */
    public function getStatus() : string
    {



        if ( $this->getBeans() < self::BEANS_USED_PER_ESPRESSO && $this->getWater() < self::LITRES_USED_PER_ESPRESSO)
        {
            return "Add beans and water";
        }

        if ( $this->getBeans() < self::BEANS_USED_PER_ESPRESSO )
        {
            return "Add beans";
        }

        if (  $this->getWater() < self::LITRES_USED_PER_ESPRESSO )
        {
            return "Add water";
        }

        $beans_left = $this->getBeans();
        $water_left = $this->getWater();

        return "$beans_left spoon of beans left and " . $water_left . " litres of water left." ;
    }


    /**
     * @param BeansContainer $container
     */
    public function setBeansContainer(BeansContainer $container)
    {
        $this->beansContainer = $container;
    }

    /**
     * @return BeansContainer
     */
    public function getBeansContainer()
    {
        return $this->beansContainer;
    }

    /**
     * @param WaterContainer $container
     */
    public function setWaterContainer(WaterContainer $container)
    {
        $this->waterContainer = $container;
    }

    /**
     * CHECK IF THE COFFE MACHINE HAS A WATER CONTAINER OR NO.
     * @return boolean
     */
    public function hasWaterContainer()
    {
        return $this->waterContainer instanceof WaterContainer;
    }

    /**
     * @return WaterContainer
     */
    public function getWaterContainer()
    {
        return $this->waterContainer;
    }

    /**
     * CHECK IF THE COFFE MACHINE HAS A BEANS CONTAINER OR NO.
     * @return boolean
     */
    public function hasBeansContainer()
    {
        return $this->beansContainer instanceof BeansContainer;
    }
}
