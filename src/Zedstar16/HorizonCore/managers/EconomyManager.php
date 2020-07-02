<?php


namespace Zedstar16\HorizonCore\managers;


use Zedstar16\HorizonCore\components\Economy;

class EconomyManager
{

    public static function get($player) : Economy{
        return new Economy($player);
    }

}