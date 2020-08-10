<?php


namespace Zedstar16\HorizonCore\components;


use Zedstar16\HorizonCore\managers\FileManager;

class TopFloatingTexts
{

    public function __construct()
    {
    }

    public function getTexts()
    {
        FileManager::getJsonData("config")["leaderboards"];
    }

}