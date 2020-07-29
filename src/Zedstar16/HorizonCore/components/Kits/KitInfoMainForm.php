<?php


namespace Zedstar16\HorizonCore\components\Kits;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class KitInfoMainForm extends BaseFormComponent
{

    public function primary()
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
        });
        $form->setTitle("Kit Info");
        $form->setContent("");
        $form->addButton("Donator Kits");
        $form->addButton("Player Kits");
        $this->p->sendForm($form);
    }
}