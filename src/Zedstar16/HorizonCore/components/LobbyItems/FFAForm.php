<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FFAManager;
use Zedstar16\HorizonCore\utils\Utils;

class FFAForm extends BaseFormComponent
{

    public function primary()
    {
        $buttons = FFAManager::getArenaNames();
        $form = new SimpleForm(function(Player $player, $data = null) use($buttons){
            if($data === null){
                return;
            }
            $button_name = $buttons[$data];
            $arena = FFAManager::getArena($button_name);
            $arena->teleportToArena($player);

        });
        $form->setTitle("FFA Arenas");
        $form->setContent("Select an arena");
        foreach($buttons as $button){
            $players = count(FFAManager::getArena($button)->getPlayers());
            $form->addButton(Utils::colorize($button)."\n§8$players §7players");
        }
        $this->p->sendForm($form);
    }


}