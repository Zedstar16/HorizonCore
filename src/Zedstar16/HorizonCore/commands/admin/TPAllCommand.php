<?php

namespace Zedstar16\HorizonCore\commands\admin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Zedstar16\HorizonCore\Horizon;

class TPAllCommand extends Command
{


    public function __construct()
    {
        parent::__construct("tpall");
        $this->description = "Teleport all online players to you";
        $this->usageMessage = "/tpall";
        $this->setPermission("horizon.tpall");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            foreach (Horizon::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $player->teleport($sender);
            }
        }else $sender->sendMessage(TextFormat::RED."Use command ingame");
    }
}
