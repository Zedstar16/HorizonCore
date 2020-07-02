<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\math\Vector3;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class ProfileForm extends BaseFormComponent
{

    public function primary()
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
        });
        $content = "§6Your XP:\n\n" . $this->getXPBarString();
        $lines = [
            "§b#1 §cTop Killer",
            "§b#1 §cLeast Deaths",
            "§b#1 §cTop KDRs",
            "§b#1 §cTop Experience",
        ];
        $content .= "\n\n§6-- Your Leaderboard Positions --\n\n" . implode("\n", $lines);
        $form->setTitle("Your ProfileForm");
        $form->setContent($content);
        $form->addButton("Close");
        $this->p->sendForm($form);
    }

    public function getXPBarString()
    {
        $xp = $this->p->getExperience();
        $lvl = $xp->calculateLevel();
        $current_level_experience = $xp->getExperience() - $xp->calculateLevelExperience($lvl);
        $xp_to_next_level = $xp->calculateLevelExperience($lvl + 1) - $xp->calculateLevelExperience($lvl);
        $progress_done = round($current_level_experience / $xp_to_next_level, 2);
        $progress_todo = 1 - $progress_done;
        $done = str_repeat("§a|", (int)($progress_done * 40));
        $todo = str_repeat("§c|", (int)($progress_todo * 40));
        return "§cLvl §6$lvl §8[$done{$todo}§8] §6EXP §f{$current_level_experience}§8/§f$xp_to_next_level";
    }

    public static function calculateBarOffset(Player $p, Vector3 $vector, int $multiplier)
    {
        $horizontal = sqrt(($vector->x - $p->x) ** 2 + ($vector->z - $p->z) ** 2);
        $vertical = $vector->y - $p->y;
        $pitch = -atan2($vertical, $horizontal) / M_PI * 180;

        $xDist = $vector->x - $p->x;
        $zDist = $vector->z - $p->z;
        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if ($yaw < 0) {
            $yaw += 360.0;
        }

        $y = -sin(deg2rad($pitch));
        $xz = cos(deg2rad($pitch));
        $x = -(-$xz * sin(deg2rad($yaw)));
        $z = -($xz * cos(deg2rad($yaw)));

        $vector = new Vector3($x, $y, $z);
        $vector->normalize()->multiply($multiplier);
        return $vector;
    }


}