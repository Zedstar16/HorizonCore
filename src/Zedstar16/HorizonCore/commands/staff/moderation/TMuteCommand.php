<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\components\Moderation\TimeFormat;

class TMuteCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tmute");
        $this->description = "Temporarily mute a player";
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        $staff = $sender->getName();
        if (isset($args[0])) {
            if (isset($args[1])) {
                $player = $server->getPlayer($args[0]) !== null ? $server->getPlayer($args[0])->getName() : $args[0];
                $online = $server->getPlayer($args[0]) !== null;
                if (isset($args[2])) {
                    try {
                        $reason = implode(" ", array_slice($args, 2));
                        $silent = false;
                        if (strpos($reason, "-s") !== false) {
                            $silent = true;
                            $reason = str_replace("-s", "", $reason);
                        }
                        $expires = TimeFormat::timeToExpiry(TimeFormat::get($args[1]));
                        ModerationAPI::mute($player, $staff, $reason, $args[1]);
                        Logger::log("Temporary Mute", $player, $staff, $reason, $args[1]);
                        if (!$silent) {
                            $server->broadcastMessage("§a$player §chas been temporarily muted by §b$staff\n§5Reason: §b$reason\n§6Duration: §e$args[1]");
                        } else {
                            $sender->sendMessage("§cYou have temporarily muted §a$player\n§5Reason: §b$reason\n§6Duration: §e$args[1]");
                            if ($online) {
                                $server->getPlayer($args[0])->sendMessage("§cYou have been temporarily muted §b$staff\n§5Reason: §b$reason\n§6Mute Expires In: §e$expires\n\n§2Appeal at: §aappeal.horizonpe.net");
                            }
                        }
                    } catch (ModerationException $exception) {
                        $sender->sendMessage("§c" . $exception->getMessage());
                    }
                } else $sender->sendMessage("§cYou need to specify a reason for the punishment");
            } else $sender->sendMessage("§cYou need to specify a duration for the punishment");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}