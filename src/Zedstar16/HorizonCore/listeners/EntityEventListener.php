<?php


namespace Zedstar16\HorizonCore\listeners;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\FloatingTextManager;

class EntityEventListener implements Listener
{

    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof HorizonPlayer) {
            $entity->updateScoreTag();
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof HorizonPlayer) {
                    $entity->last_damager[0] = [
                        "time" => microtime(true),
                        "player" => $damager->getName()
                    ];
                    $damager->getSession()->incrementValue("hits");
                    $damager->getSession()->incrementValue("damage_dealt", $event->getBaseDamage());
                    $damager->lasthit = time();
                    $entity->lasthit = time();
                }
            }
            $entity->getSession()->incrementValue("damage_taken", $event->getBaseDamage());
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $to = $event->getTarget();
            if (!FloatingTextManager::isLoadedIn($to)) {
                FloatingTextManager::loadIn($to);
            }
            foreach ($event->getOrigin()->getPlayers() as $player) {
                if ($player instanceof HorizonPlayer) {
                    $player->getSession()->getScoreboard()->updateLine("Area Players", count($player->getLevel()->getPlayers()));
                    $player->getSession()->getScoreboard()->updateLine("Arena Players", count($player->getLevel()->getPlayers()));
                }
            }
            foreach ($event->getTarget()->getPlayers() as $player) {
                if ($player instanceof HorizonPlayer) {
                    $player->getSession()->getScoreboard()->updateLine("Area Players", count($player->getLevel()->getPlayers()));
                    $player->getSession()->getScoreboard()->updateLine("Arena Players", count($player->getLevel()->getPlayers()));
                }
            }
        }
    }
}