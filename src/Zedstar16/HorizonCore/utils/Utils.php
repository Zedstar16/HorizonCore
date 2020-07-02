<?php


namespace Zedstar16\HorizonCore\utils;


use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use TypeError;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;

class Utils
{

    public static function isInsidePracticeZone(Vector3 $vector3)
    {
        $max = Horizon::Config()["praczone"]["max"];
        $min = Horizon::Config()["praczone"]["min"];
        $aabb = new AxisAlignedBB($min["x"], $min["y"], $min["z"], $max["x"], $max["y"], $max["z"]);
        return $aabb->isVectorInside($vector3);
    }

    public static function getNamed(string $string)
    {
        $names = [
            "ffa" => "FFA",
            "duels" => "Duels",
            "kit" => "Kit",
            "kitpvp" => "KitPvP",
            "nodebuff" => "NoDebuff",
            "gapple" => "Gapple",
            "buildpvp" => "BuildPvP",
            "fist" => "Fist",
            "combo" => "Combo"
        ];
        return $names[$string] ?? $string;
    }

    public static function colorize(string $string, $bold = false)
    {
        $string = strtolower($string);
        $names = [
            "ffa" => "§dFFA",
            "duels" => "§6Duels",
            "nodebuff" => "§cNoDebuff",
            "gapple" => "§6Gapple",
            "buildpvp" => "§9BuildPvP",
            "fist" => "§2Fist",
            "combo" => "§bCombo"
        ];
        return $bold ? "§l" . $names[$string] : $names[$string] ?? $string;
    }

    public static function stringify($player): string
    {
        if ($player instanceof Player) {
            $username = $player->getLowerCaseName();
        } elseif (is_string($player)) {
            $username = strtolower($player);
        } else {
            throw new TypeError("Expected player object or string for Stringify function");
        }
        return $username;
    }

    public static function centralise(string $string): string
    {
        $lines = explode("\n", $string);
        $longest = 0;
        foreach ($lines as $line) {
            $len = strlen($line);
            if ($len > $longest) {
                $longest = $len;
            }
        }
        $midpoint = (int)$longest / 2;
        $result = "";
        foreach ($lines as $line) {
            $len = strlen($line);
            $line_mid = (int)$len / 2;
            $dist = $midpoint - $line_mid;
            $result .= str_repeat(" ", $dist) . $line . "\n";
        }
        return $result;
    }

    public static function getReceipients(HorizonPlayer $p)
    {
        //if($p->)
    }


}