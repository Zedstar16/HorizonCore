<?php


namespace Zedstar16\HorizonCore\events;

use Zedstar16\HorizonCore\components\Experience\Experience;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\ExperienceManager;

class AddXPEvent extends HorizonPlayerEvent
{

    public function __construct(HorizonPlayer $player)
    {
        parent::__construct($player);
    }

    public function getExperience(): Experience
    {
        return ExperienceManager::get($this->player);
    }

}