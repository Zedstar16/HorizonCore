<?php

namespace Zedstar16\HorizonCore\tasks\threaded;

use pocketmine\Thread;
use pocketmine\utils\Internet;
use Zedstar16\HorizonCore\cache\Cache;

class DiscordDispatchThread extends Thread
{


    public function __construct()
    {
        $this->start(PTHREADS_INHERIT_NONE);
    }

    public function run()
    {
        while (true) {
            usleep(50);
            foreach (Cache::$discord_dispatch as $key => $data) {
                unset(Cache::$discord_dispatch[$key]);
                Internet::postURL($data["url"], json_encode($data["data"]), 2, ["Content-Type: application/json"]);
            }
        }
    }
}