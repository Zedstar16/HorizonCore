<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\utils\Utils;

class KickCommand extends Command
{

    public function __construct()
    {
        parent::__construct("kick");
        $this->description = "Kick a player from the server";
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        if (isset($args[0])) {
            if (isset($args[1])) {
                $p = $server->getPlayer($args[0]);
                if ($p !== null) {
                    try {
                        array_shift($args);
                        $reason = implode(" ", $args);
                        $silent = false;
                        $player = $p->getName();
                        $staff = $sender->getName();
                        if (strpos($reason, "-s") !== false) {
                            $silent = true;
                            $reason = str_replace("-s", "", $reason);
                        }
                        ModerationAPI::kick($player, $staff, $reason);
                        Logger::log($silent ? "Silent Kick" : "Kick", $player, $staff, $reason);
                        if (!$silent) {
                            $server->broadcastMessage("§a$player §chas been kicked by §b$staff\n§5Reason: §b$reason");
                        } else {
                            $sender->sendMessage("§cYou have kicked §a$player\n§5Reason: §b$reason");
                        }
                        $message = Utils::centralise("§cYou have been kicked from the server by §b{$sender->getName()}\n§5Reason: §b" . $reason);
                        $p->kick($message, false);
                    } catch (ModerationException $exception) {
                        $sender->sendMessage("§c" . $exception->getMessage());
                    }
                } else $sender->sendMessage("§cSpecified player is not online");
            } else $sender->sendMessage("You must provide a reason for the punishment");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}