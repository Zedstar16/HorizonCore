<?php


namespace Zedstar16\HorizonCore\components\Moderation;

use Zedstar16\HorizonCore\managers\FileManager;

class ModerationAPI
{

    /*
     * Yes
     * I am fully aware that it'd be a lot more efficient if this class wasn't all static functions and all the data was retrieved on the class' construction
     * Rather than having static functions where the data is fetched every time, like now
     * However i did too much of it at this point to bother going back and changing it so it is gonna stay how it is ig
     *
     */

    public static function ban(string $player, string $moderator, string $reason, string $duration = null)
    {
        $bans = FileManager::getJsonData("bans");
        if (isset($bans[$player])) {
            throw new ModerationException("$player is already banned");
        }
        $history = FileManager::getJsonData("history");
        $type = $duration === null ? "Permanent Ban" : "Temp Ban";
        $history[strtolower($player)][] = [
            "action" => $type,
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? "Never" : TimeFormat::get($duration)->format("r")
        ];
        $bans[strtolower($player)] = [
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? null : TimeFormat::get($duration)->format("r")
        ];
        FileManager::saveJsonData("bans", $bans);
        FileManager::saveJsonData("history", $history);
    }


    public static function editBan(string $player, string $moderator, string $reason, string $duration = null)
    {
        $bans = FileManager::getJsonData("bans");
        if (!isset($bans[$player])) {
            throw new ModerationException("$player is not banned");
        }
        $history = FileManager::getJsonData("history");
        $type = $duration === null ? "Ban updated to Permanent" : "Ban updated to Temporary";
        $history[strtolower($player)][] = [
            "action" => $type,
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? "Never" : TimeFormat::get($duration)->format("r")
        ];
        $bans[strtolower($player)] = [
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? null : TimeFormat::get($duration)->format("r")
        ];
        FileManager::saveJsonData("history", $history);
        FileManager::saveJsonData("bans", $bans);
    }

    public static function mute(string $player, string $moderator, string $reason, string $duration = null)
    {
        $mutes = FileManager::getJsonData("mutes");
        if (isset($mutes[$player])) {
            throw new ModerationException("$player is already muted");
        }
        $mutes[strtolower($player)] = [
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? null : TimeFormat::get($duration)->format("r")
        ];
        FileManager::saveJsonData("mutes", $mutes);
        $history = FileManager::getJsonData("history");
        $type = $duration === null ? "Permanent Mute" : "Temp Mute";
        $history[strtolower($player)][] = [
            "action" => $type,
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? "Never" : TimeFormat::get($duration)->format("r")
        ];
        if ($duration !== null) {
            $history[strtolower($player)][array_key_last($history[strtolower($player)])]["duration"] = $duration;
        }
        FileManager::saveJsonData("history", $history);
    }

    public static function editMute(string $player, string $moderator, string $reason, string $duration = null)
    {
        $mutes = FileManager::getJsonData("mutes");
        if (!isset($mutes[$player])) {
            throw new ModerationException("$player is not muted");
        }
        $history = FileManager::getJsonData("history");
        $type = $duration === null ? "Mute updated to Permanent" : "Mute updated to Temporary";
        $history[strtolower($player)][] = [
            "action" => $type,
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? "Never" : TimeFormat::get($duration)->format("r")
        ];
        $mutes[strtolower($player)] = [
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
            "expires" => $duration === null ? null : TimeFormat::get($duration)->format("r")
        ];
        FileManager::saveJsonData("history", $history);
        FileManager::saveJsonData("mutes", $mutes);
    }

    public static function kick(string $player, string $moderator, string $reason)
    {
        $history = FileManager::getJsonData("history");
        $history[strtolower($player)][] = [
            "action" => "Kick",
            "moderator" => $moderator,
            "reason" => $reason,
            "executed" => TimeFormat::getCurrentDate(),
        ];
        FileManager::saveJsonData("history", $history);
    }

    public static function unmute(string $player, string $moderator)
    {
        $mutes = FileManager::getJsonData("mutes");
        if (isset($mutes[$player])) {
            unset($mutes[$player]);
            $history = FileManager::getJsonData("history");
            $history[strtolower($player)][] = [
                "action" => "Unmute",
                "moderator" => $moderator,
                "executed" => TimeFormat::getCurrentDate(),
            ];
            FileManager::saveJsonData("history", $history);
            FileManager::saveJsonData("mutes", $mutes);
        } else throw new ModerationException("$player is not muted");
    }

    public static function unban(string $player, string $moderator)
    {
        $bans = FileManager::getJsonData("bans");
        if (isset($bans[$player])) {
            unset($bans[$player]);
            $history = FileManager::getJsonData("history");
            $history[strtolower($player)][] = [
                "action" => "Unban",
                "moderator" => $moderator,
                "executed" => TimeFormat::getCurrentDate(),
            ];
            FileManager::saveJsonData("history", $history);
            FileManager::saveJsonData("bans", $bans);
        } else throw new ModerationException("$player is not banned");
    }

    public static function getBan($player)
    {
        $bans = FileManager::getJsonData("bans");
        if (isset($bans[$player])) {
            return $bans[$player];
        } else throw new ModerationException("$player is not banned");
    }

    public static function getMute($player)
    {
        $mutes = FileManager::getJsonData("mutes");
        if (isset($mutes[$player])) {
            return $mutes[$player];
        } else throw new ModerationException("$player is not muted");
    }

    public static function isBanned($player): bool
    {
        $bans = FileManager::getJsonData("bans");
        if (isset($bans[$player])) {
            $time = $bans[$player]["expires"];
            if ($time !== null) {
                $expiry = strtotime($time);
                if (time() >= $expiry) {
                    unset($bans[$player]);
                    FileManager::saveJsonData("bans", $bans);
                    return false;
                }
            }
        }
        return isset($bans[$player]);
    }

    public static function isMuted($player): bool
    {
        $mutes = FileManager::getJsonData("mutes");
        if (isset($mutes[$player])) {
            $time = $mutes[$player]["expires"];
            if ($time !== null) {
                $expiry = strtotime($time);
                if (time() >= $expiry) {
                    unset($mutes[$player]);
                    FileManager::saveJsonData("mutes", $mutes);
                    return false;
                }
            }
        }
        return isset($mutes[$player]);
    }

}