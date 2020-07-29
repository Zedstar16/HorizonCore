<?php


namespace Zedstar16\HorizonCore\commands\staff;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\WatchList;
use Zedstar16\HorizonCore\HorizonPlayer;

class WatchCommand extends Command
{


    public function __construct()
    {
        parent::__construct("watch");
        $this->description = "Spectate players";
        $this->setAliases(["unwatch"]);
        $this->setPermission("horizon.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof HorizonPlayer) {
            $username = $sender->getName();
            if ($commandLabel == "watch") {
                if (isset($args[0])) {
                    $target = Server::getInstance()->getPlayer($args[0]);
                    if ($target !== null) {
                        if (isset(WatchList::$watching[$username])) {
                            WatchList::$watching[$username]["target"] = $target->getName();
                            $sender->teleport($target);
                        } else {
                            $this->setWatching($sender, $target, (bool)$args[1] ?? false);
                        }
                        $sender->sendMessage("You are now watching {$target->getName()}");
                    } else $sender->sendMessage("Target is not online");
                } else $sender->sendMessage("/watch (player)");
            } elseif ($commandLabel == "unwatch") {
                $username = $sender->getName();
                if (isset(WatchList::$watching[$username])) {
                    $data = WatchList::$watching[$username];
                    $sender->teleport($data["original_pos"]);
                    $sender->setFlying(false);
                    $sender->setInvisible(false);
                    $sender->getInventory()->setContents($data["inventory"]);
                    $sender->getArmorInventory()->setContents($data["armor"]);
                    unset(WatchList::$watching[$username]);
                    $sender->sendMessage("You have left watch mode");
                } else $sender->sendMessage("You are not current watching a player");
            }
        }
    }

    public function setWatching(HorizonPlayer $staff, Player $target, $lock = false)
    {
        $staffname = $staff->getName();
        WatchList::$watching[$staffname] = [
            "target" => $target->getName(),
            "locked" => $lock,
            "inventory" => $staff->getInventory()->getContents(),
            "armor" => $staff->getArmorInventory()->getContents(),
            "original_pos" => $staff->getLocation()
        ];
        $staff->clearInventory();
        $staff->setFlying(true);
        $staff->setInvisible(true);
        $staff->teleport($target);
    }
}