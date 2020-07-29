<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\utils\Utils;

class BanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("ban");
        $this->description = "Ban a player";
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        $staff = $sender->getName();
        if (isset($args[0])) {
            if (isset($args[1])) {
                try {
                    $player = $server->getPlayer($args[0]) !== null ? $server->getPlayer($args[0])->getName() : $args[0];
                    $online = $server->getPlayer($args[0]) !== null;
                    array_shift($args);
                    $reason = implode(" ", $args);
                    ModerationAPI::ban($player, $staff, $reason);
                    $silent = false;
                    if (strpos($reason, "-s") !== false) {
                        $silent = true;
                        $reason = str_replace("-s", "", $reason);
                    }
                    if ($online) {
                        $server->getPlayer($args[0])->kick(Utils::centralise("You have permanently banned by §b$staff\n§5Reason: §b$reason\n\n§2Appeal at: §aappeal.horizonpe.net"));
                    }
                    Logger::log("Permanent Ban", $player, $staff, $reason);
                    if (!$silent) {
                        $server->broadcastMessage("§a$player §chas been permanently banned by §b$staff\n§5Reason: §b$reason");
                    } else {
                        $sender->sendMessage("§cYou have permanently banned §a$player\n§5Reason: §b$reason");
                    }
                } catch (ModerationException $exception) {
                    $sender->sendMessage("§c" . $exception->getMessage());
                }
            } else $sender->sendMessage("§cYou need to specify a reason for the punishment");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}