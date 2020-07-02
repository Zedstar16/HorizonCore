<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\components\KitEditor;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\PlayerDataManager;

class StatsForm extends BaseFormComponent
{

    public function primary()
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
        });
        $d = PlayerDataManager::getData($this->p);
        $kdr = $d["deaths"] > 0 ? round(($d["kills"] / $d["deaths"]), 2) : 0;
        $lines = [
            "§6--- PvP Stats ---",
            "§f➣ §cKills: §6$d[kills]",
            "§f➣ §cDeaths: §6$d[deaths]",
            "§f➣ §cKRD: §6$kdr",
            "§f➣ §cHits: §6$d[hits]",
            "§f➣ §cClicks: §6$d[clicks]",
            "§f➣ §cDamage Dealt: §6$d[damage_dealt]",
            "§f➣ §cDamage Taken: §6$d[damage_taken]",
            "§f➣ §cTotal Experience: §6$d[experience]",
            "",
            "§6--- Misc Stats ---",
            "§f➣ §cDistance Travalled: §6$d[distance_travelled]",
            "§f➣ §cJoins: §6$d[joins]",
            "§f➣ §cChat Messages: §6$d[chat_messages]",
            "§f➣ §cItems Dropped: §6$d[items_dropped]",
            "§f➣ §cItems Picked Up: §6$d[items_picked_up]",
            "§f➣ §cItems Consumed: §6$d[items_consumed]",
            "§f➣ §cBlocks Placed: §6$d[blocks_placed]",
            "§f➣ §cBlocks Broken: §6$d[blocks_broken]",
        ];
        $form->setTitle("Your Statistics");
        $form->setContent(implode("\n", $lines));
        $form->addButton("Close");
        $this->p->sendForm($form);
    }

}