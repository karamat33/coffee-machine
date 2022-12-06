<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $redis = Redis::connection();
        $redis->set('settings:1:BEANS_CONTAINER_CAPACITY', 50);
        $redis->set('settings:1:WATER_CONTAINER_CAPACITY', 2);
    }
}
