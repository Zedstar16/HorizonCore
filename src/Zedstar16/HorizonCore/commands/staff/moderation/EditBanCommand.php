<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;

class EditBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("editban");
        $this->description = "Edit a player's ban";
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $staff = $sender->getName();
        if (isset($args[0])) {
            if (isset($args[1])) {
                if (isset($args[2])) {
                    try {
                        $temp = $args[1] !== "perm";
                        $previous_temp = ModerationAPI::getBan($args[0])["expires"] !== null;
                        $duration = null;
                        if ($temp) {
                            $duration = $args[1];
                        }
                        $player = $args[0];
                        $reason = implode(" ", array_slice($args, 2));
                        ModerationAPI::editBan($player, $staff, $reason, $duration);
                        Logger::log("Ban Updated to " . ($temp ? "Temp" : "Permanent") . " from " . ($previous_temp ? "Temp" : "Permanent"), $player, $staff, $reason, $temp ? $args[1] : null);
                        $sender->sendMessage("§cBan for §a$player §cupdated to §b" . ($temp ? "Temp" : "Permanent") . "§c from §b" . ($previous_temp ? "Temp" : "Permanent") . "§c with §5Reason: §d$reason");
                    } catch (ModerationException $exception) {
                        $sender->sendMessage("§c" . $exception->getMessage());
                    }
                } else $sender->sendMessage("§cYou must provide an updated reason");
            } else $sender->sendMessage("§cYou must provide an updated time: <perm/timeformat>");
        } else $sender->sendMessage("§cUsage: /editban <time: perm/timeformat> <player> <reason>");
    }
}