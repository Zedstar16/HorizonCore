<?php


namespace Zedstar16\HorizonCore\managers;


use Zedstar16\HorizonCore\components\Experience\Experience;

class ExperienceManager
{

    public static function get($player): Experience
    {
        return new Experience($player);
    }

}