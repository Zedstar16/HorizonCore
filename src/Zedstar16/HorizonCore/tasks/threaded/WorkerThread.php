<?php


namespace Zedstar16\HorizonCore\tasks\threaded;


use pocketmine\Thread;

class WorkerThread extends Thread
{

    public const UPDATE_LEADERBOARD_DATA = 0;
    public const UPDATE_PLAYER_STATS = 1;

    public static $tasks = [];

    public function __construct()
    {
        $this->start(PTHREADS_INHERIT_NONE);
    }

    public function loop()
    {
        while (is_int((round(microtime(true), 3) * 1000) / 25)) {
            if (!empty(self::$tasks)) {

            }
        }
    }


}