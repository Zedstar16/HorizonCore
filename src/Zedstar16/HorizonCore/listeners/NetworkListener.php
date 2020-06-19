<?php


namespace Zedstar16\HorizonCore\listeners;


use Bavfalcon9\MultiVersion\Protocols\v1_12_0\Packets\LoginPacket;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use Zedstar16\HorizonCore\Horizon;

class NetworkListener implements Listener
{

    public function onDataPacketReceive(DataPacketReceiveEvent $event){
        $p = $event->getPlayer();
        $pk = $event->getPacket();
        if($pk instanceof LoginPacket){
            Horizon::$players[$event->getPlayer()->getName()] = $pk->clientData;
        }
    }


}