<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class FFAForm
{
    /** @var Player */
    public $p;

    public function __construct(Player $player)
    {
        $this->p = $player;
        $this->primary();
    }

    public function primary()
    {

        $form = new SimpleForm(function(Player $player, $data = null){
            if($data === null){
                return;
            }
        });
        $form->setTitle("FFA");
        $form->setContent("Select an arena");
        $this->p->sendForm($form);
    }


}