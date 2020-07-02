<?php


namespace Zedstar16\HorizonCore\components;

use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\utils\Utils;

class Economy
{
    /** @var String */
    private $p;
    /** @var Int */
    private $coins;

    public function __construct($player)
    {
        $this->p = Utils::stringify($player);
        $this->coins = PlayerDataManager::getData($this->p)["coins"];
    }


    public function addCoins(int $amount)
    {
        $this->coins += $amount;
        $this->save();
    }

    public function getCoins()
    {
        return 1500;
    }

    public function removeCoins(int $amount)
    {
        $this->coins -= $amount;
        $this->save();
    }

    public function setCoins(Int $amount){
        $this->coins = $amount;
        $this->save();
    }

    private function save()
    {
        $data = PlayerDataManager::getData($this->p);
        $data["coins"] = $this->coins;
        PlayerDataManager::saveData($this->p, $data);
    }

}