<?php


namespace Zedstar16\HorizonCore\commands\economy;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\managers\EconomyManager;
use Zedstar16\HorizonCore\utils\Utils;

class GiveCoinsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("givecoins");
        $this->description = "Give coins to a player";
        $this->usageMessage = "/givecoins <player> <coins>";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $usage = "/givecoins (player) (coins)";
        if(count($args) == 2){
            if(is_numeric($args[1])) {
                $p = Server::getInstance()->getPlayer($args[0]);
                $player = $p !== null ? Utils::stringify($p) : Utils::stringify($args[0]);
                EconomyManager::get($player)->addCoins($args[1]);
                $sender->sendMessage("Successfully added $args[1] coins to $player's balance");
            }else $sender->sendMessage($usage);
        }else $sender->sendMessage($usage);
    }

}
