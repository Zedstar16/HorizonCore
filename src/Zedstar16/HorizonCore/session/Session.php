<?php


namespace Zedstar16\HorizonCore\session;


use Zedstar16\HorizonCore\components\BossBarTitles;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\BossBar;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\DiverseBossBar;
use Zedstar16\HorizonCore\tasks\BossBarUpdateTask;

class Session
{

    private $player;

    public $data = [];

    /** @var DiverseBossBar */
    public $bossbar;

    public $clientData = [];

    public function __construct(HorizonPlayer $player)
    {
        $this->player = $player;
      //  $player->teleportToSpawn();
        $data = [
            "damage_taken",
            "damage_dealt",
            "clicks",
            "hits",
            "distance_travelled",
            "kills",
            "deaths",
            "chat_messages",
            "dropped_items",
            "items_picked_up",
            "items_consumed",
            "blocks_placed",
            "blocks_broken",
        ];
        $this->data = array_fill_keys($data, 0);
        $this->bossbar = new DiverseBossBar();
        $this->bossbar->addPlayer($player);
        $this->bossbar->setTitle("§bPlaying on §6Horizon§cPE");
        $this->bossbar->setSubTitle(BossBarTitles::TITLES[0]);
        Horizon::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new BossBarUpdateTask($player), 50, 80);
    }

    public function getPlayer(): HorizonPlayer
    {
        return $this->player;
    }

    public function click()
    {
        $this->data["clicks"]++;
    }

    public function getPlayerData(){
        $return = [];
        $os = ["Unknown", "Android", "iOS", "macOS", "FireOS", "GearVR", "HoloLens", "Windows 10", "Windows", "Dedicated", "Orbis", "NX"];
        $UI = ["Classic UI", "Pocket UI"];
        $Controls = ["Unknown", "Mouse", "Touch", "Controller"];
        $GUI = [-2 => "Minimum", -1 => "Medium", 0 => "Maximum"];
        $cdata = $this->clientData;
        $return["DeviceID"] = $cdata["DeviceId"];
        $return["OS"] = $os[$cdata["DeviceOS"]];
        $return["UI"] = $UI[$cdata["UIProfile"]];
        $return["GUI"] = $GUI[$cdata["GuiScale"]];
        $return["Controls"] = $Controls[$cdata["CurrentInputMode"]];
        return $return;
    }

    public function getSessionStats()
    {
        return $this->data;
    }

    public function getBossBar() : DiverseBossBar{
        return $this->bossbar;
    }


}