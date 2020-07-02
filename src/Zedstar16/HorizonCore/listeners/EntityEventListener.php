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
        if($entity instanceof HorizonPlayer) {
            $entity->updateScoreTag();
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof HorizonPlayer) {
                    $damager->getSession()->incrementValue("hits");
                    $damager->getSession()->incrementValue("damage_dealt", $event->getFinalDamage());
                    $damager->lasthit = time();
                    $entity->lasthit = time();
                }
            }
            $entity->getSession()->incrementValue("damage_taken", $event->getFinalDamage());
        }
    }
}