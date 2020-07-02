<?php


namespace Zedstar16\HorizonCore\managers;


use Zedstar16\HorizonCore\utils\Utils;

class ExperienceManager
{

    public const XP_SOURCES =
        [

        ];

    public static function addExperience($player){

    }


    public static function getExperience($player) : ?Int{
        return 382;
    }

    public static function calculateLevel($player){
        $xp = self::getExperience(Utils::stringify($player));
        return 1;
    }

    public static function calculateLevelExperience(Int $level){
        if($level == 1){
            return 0;
        }else{
            return 500;
        }
    }

}