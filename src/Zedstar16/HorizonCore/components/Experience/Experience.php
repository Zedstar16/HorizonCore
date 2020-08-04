<?php


namespace Zedstar16\HorizonCore\components\Experience;


use Zedstar16\HorizonCore\events\AddXPEvent;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\utils\Utils;

class Experience
{

    public const KILL = 50;
    public const VOTE = 750;
    public const CHAT = 5;

    /** @var String */
    private $p;
    /** @var Int */
    private $experience;

    public function __construct($player)
    {
        $this->p = Utils::stringify($player);
        $this->experience = PlayerDataManager::getData($this->p)["experience"];
    }

    public function addExperience(int $amount)
    {
        $p = Horizon::getPlayer($this->p);
        if ($p !== null) {
            new AddXPEvent($p);
        }
        $this->experience += $amount;
        $this->save();
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function getLevel()
    {
        return $this->calculateLevel();
    }

    public function setExperience(int $amount)
    {
        $this->experience = $amount;
        $this->save();
    }

    private function save()
    {
        $data = PlayerDataManager::getData($this->p);
        $data["experience"] = $this->experience;
        PlayerDataManager::saveData($this->p, $data);
    }

    public function calculateLevel()
    {
        return (int)floor((sqrt(1225 + (100 * $this->experience)) - 35) / 70);
    }

    public function calculateXPToNextLevel()
    {
        return $this->calculateLevelExperience($this->calculateLevel() + 1) - $this->experience;
    }

    public function calculatePercentageProgression()
    {
        $level = $this->calculateLevel();
        $current_xp_in_level = $this->experience - $this->calculateLevelExperience($level);
        $total_xp_for_level = $this->calculateLevelExperience($level + 1) - $this->calculateLevelExperience($level);
        return $current_xp_in_level / $total_xp_for_level;
    }

    public function calculateLevelExperience(int $level)
    {
        // FORMULA = 35 * (LEVEL) * (1 + LEVEL) = EXP
        return 35 * $level * ($level + 1);
    }


}