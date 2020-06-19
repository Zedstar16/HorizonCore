<?php


namespace Zedstar16\HorizonCore\listeners;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;

class EntityEventListener implements Listener
{

    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($entity instanceof HorizonPlayer && $damager instanceof HorizonPlayer){
                $damager->lasthit = time();
                $entity->lasthit = time();
                $health = round($entity->getHealth());
                $color = $this->getHealthColor($health);
                $entity->setScoreTag("{$color}{$health}❤");
            }
        }
    }

    public function getHealthColor($health) : string{
        $color = '';
        if($health >= 16){
            $color = "§a";
        }elseif($health < 16 && $health >= 12){
            $color = "§e";
        }elseif($health < 12 && $health >= 8){
            $color = "§6";
        }elseif($health < 8 && $health >= 4){
            $color = "§c";
        }elseif($health < 4 && $health >= 0){
            $color = "§4";
        }
        return $color;
    }
}