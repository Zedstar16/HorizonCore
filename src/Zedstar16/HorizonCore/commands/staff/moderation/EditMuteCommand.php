<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;

class EditMuteCommand extends Command
{

    public function __construct()
    {
        parent::__construct("editmute");
        $this->description = "Edit a player's mute";
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
                        $previous_temp = ModerationAPI::getMute($args[0])["expires"] !== null;
                        $duration = null;
                        if ($temp) {
                            $duration = $args[1];
                        }
                        $player = $args[0];
                        array_shift($args);
                        $reason = implode(" ", $args);
                        ModerationAPI::editMute($player, $staff, $reason, $duration);
                        Logger::log("Mute Updated to " . ($temp ? "Temp" : "Permanent") . " from " . ($previous_temp ? "Temp" : "Permanent"), $player, $staff, $reason, $temp ? $args[1] : null);
                        $sender->sendMessage("§cMute for §a$player §cupdated to §b" . ($temp ? "Temp" : "Permanent") . "§c from §b" . ($previous_temp ? "Temp" : "Permanent") . "§c with §5Reason: §d$reason");
                    } catch (ModerationException $exception) {
                        $sender->sendMessage("§c" . $exception->getMessage());
                    }
                } else $sender->sendMessage("§cYou must provide an updated reason");
            } else $sender->sendMessage("§cYou must provide an updated time: <perm/timeformat>");
        } else $sender->sendMessage("§cUsage: /editmute <time: perm/timeformat> <player> <reason>");
    }

}