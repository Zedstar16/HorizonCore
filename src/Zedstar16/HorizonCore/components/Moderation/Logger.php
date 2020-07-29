<?php


namespace Zedstar16\HorizonCore\components\Moderation;


use DateTime;
use DateTimeZone;
use pocketmine\utils\Internet;

class Logger
{
    public static function log(string $type, string $offender, string $moderator, string $reason = "", $expires = null, $extra = "")
    {
        $url = "https://discordapp.com/api/webhooks/729392544286245025/L0deyCudrc64sHtdNxGDO0qCiZWBnvHWIysFySk-MKuIhLqC9P5iSioGYHSdedsexlci";
        $color = [
            "Permanent Ban" => 0xfc0303,
            "Temp Ban" => 0xf50a64,
            "Kick" => 0x1,
            "Temp Mute" => 0x1,
            "Unban" => 0x1,
            "Unmute" => 0x1
        ];
        $reason = strlen($reason) > 1 ? "\n**Reason:** $reason" : "";
        $duration = $expires !== null ? "\n**Expires:** $expires" : "";
        $data = [];
        $timestamp = new DateTime();
        $timestamp->setTimezone(new DateTimeZone("UTC"));
        $data["embeds"][] = [
            "color" => 0x5de0bb,
            "title" => $type,
            "description" => "**Offender:** $offender\n**Moderator:** $moderator" . "$reason" . "$duration" . "$extra",
            "timestamp" => $timestamp->format("Y-m-d\TH:i:s.v\Z"),
            "footer" => [
                "text" => "Executed"
            ]
        ];
        Internet::postURL($url, json_encode($data), 2, ["Content-Type: application/json"]);

    }
}