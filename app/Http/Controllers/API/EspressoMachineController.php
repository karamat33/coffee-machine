<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\EspressoMachineClass;
use App\Classes\BeansContainerClass;
use App\Classes\WaterContainerClass;
use Illuminate\Support\Facades\Redis;


class EspressoMachineController extends Controller
{

    const BEANS_CONTAINER_CAPACITY = 50;
    const WATER_CONTAINER_CAPACITY = 2;

    /**
     * @return EspressoMachineClass
     * @throws \App\Classes\ContainerFullException
     */
    public function createMachine(){

        $machine = new EspressoMachineClass();

        $machine->setBeansContainer(new BeansContainerClass(self::BEANS_CONTAINER_CAPACITY));
        $machine->setWaterContainer(new WaterContainerClass(self::WATER_CONTAINER_CAPACITY));

        $redis = Redis::connection();

        $beansContainerCapacity = $redis->get('settings:1:BEANS_CONTAINER_CAPACITY') == null ? 50 : $redis->get('settings:1:BEANS_CONTAINER_CAPACITY');
        $waterContainerCapacity = $redis->get('settings:1:WATER_CONTAINER_CAPACITY') == null ? 2 : $redis->get('settings:1:WATER_CONTAINER_CAPACITY');

        $machine->addBeans($beansContainerCapacity);
        $machine->addWater($waterContainerCapacity);

        return $machine;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Classes\ContainerFullException
     * @throws \App\Classes\NoBeansException
     */
    public function makeEspresso(Request $request)
    {
        $machine = $this->createMachine();
        $machine->makeEspresso();
        return response()->json(['status' => $machine->getStatus()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Classes\ContainerFullException
     * @throws \App\Classes\NoBeansException
     */
    public function makeDoubleEspresso(Request $request)
    {
        $machine = $this->createMachine();
        $machine->makeDoubleEspresso();
        return response()->json(['status' => $machine->getStatus()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Classes\ContainerFullException
     */
    public function getStatus(Request $request)
    {
        $machine = $this->createMachine();
        return response()->json(['status' => $machine->getStatus()]);
    }
}
