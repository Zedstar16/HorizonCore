<?php


namespace Zedstar16\HorizonCore\session;


use pocketmine\scheduler\ClosureTask;
use Zedstar16\HorizonCore\components\BossBarTitles;
use Zedstar16\HorizonCore\components\Economy;
use Zedstar16\HorizonCore\components\Experience\Experience;
use Zedstar16\HorizonCore\components\HUD\Scoreboard;
use Zedstar16\HorizonCore\events\AddKillStreakEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\DiverseBossBar;
use Zedstar16\HorizonCore\tasks\BossBarUpdateTask;

class Session
{

    private $player;

    public $data = [];

    /** @var DiverseBossBar */
    public $bossbar;

    public $clientData = [];

    public $killstreak = 1;
    /** @var Scoreboard */
    private $scoreboard;
    /** @var Economy */
    private $economy;
    /** @var Experience */
    private $experience;

    public function __construct(HorizonPlayer $player)
    {
        $this->player = $player;
        $data = [
            "damage_taken",
            "damage_dealt",
            "clicks",
            "hits",
            "distance_travelled",
            "kills",
            "deaths",
            "chat_messages",
            "items_dropped",
            "items_picked_up",
            "items_consumed",
            "blocks_placed",
            "blocks_broken",
        ];
        $this->clientData = Horizon::$players[$player->getName()]["clientData"];
        $this->data = array_fill_keys($data, 0);
        //$this->initialiseProperties();
        Horizon::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new BossBarUpdateTask($player), 50, 80);
    }

    public function initialiseProperties(): void
    {
        // Horizon::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (Int $currentTick) : void{
        $this->bossbar = new DiverseBossBar();
        $this->bossbar->addPlayer($this->player);
        $this->bossbar->setTitle("§bPlaying on §6Horizon§cPE");
        $this->bossbar->setSubTitle(BossBarTitles::TITLES[0]);
        $this->scoreboard = new Scoreboard($this->player);
        $this->economy = new Economy($this->player);
        $this->experience = new Experience($this->player);
        //  }), 5);
    }

    public function getPlayer(): HorizonPlayer
    {
        return $this->player;
    }

    public function getPlayerData()
    {
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

    public function getKillStreak()
    {
        return $this->killstreak;
    }

    public function addKillToStreak()
    {
        new AddKillStreakEvent($this->getPlayer());
        return $this->killstreak++;
    }

    public function resetKillStreak()
    {

    }


    public function getSessionStats()
    {
        return $this->data;
    }

    public function incrementValue($key, $value = 1)
    {
        $this->data[$key] += $value;
    }

    public function getBossBar(): ?DiverseBossBar
    {
        return $this->bossbar;
    }

    public function getEconomy(): ?Economy
    {
        return $this->economy;
    }

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function getScoreboard(): ?Scoreboard
    {
        return $this->scoreboard;
    }

}