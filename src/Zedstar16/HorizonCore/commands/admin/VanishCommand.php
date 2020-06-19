<?php

namespace Zedstar16\HorizonCore\commands\admin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Zedstar16\HorizonCore\Horizon;

class VanishCommand extends Command
{

    public function __construct()
    {
        parent::__construct("vanish");
        $this->description = "Vanish yourself from other players";
        $this->usageMessage = "/vanish <player>";
        $this->setPermission("horizon.vanish");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            if (Server::getInstance()->getPlayer($args[0]) !== null) {
                $p = Server::getInstance()->getPlayer($args[0]);
                $this->vanish($p);
                $sender->sendMessage(Horizon::prefix . "§aSuccessfully toggled vanish for §b{$p->getName()}");
            } else $sender->sendMessage("§cTarget not online");
        } else {
            if ($sender instanceof Player) {
                $this->vanish($sender);
            }
        }
    }

    public function vanish(Player $player)
    {
        $n = $player->getName();
        if(!isset(Horizon::$vanished[$n])) {
            $player->hidePlayer($player);
            Server::getInstance()->removePlayerListData($player->getUniqueId());
            $player->setFlying(true);
        }else{
            $player->showPlayer($player);
            Server::getInstance()->updatePlayerListData($player->getUniqueId(), $player->getOwningEntityId(), $player->getName(), $player->getSkin(), $player->getXuid());
            $player->setFlying(false);
        }
    }


}
