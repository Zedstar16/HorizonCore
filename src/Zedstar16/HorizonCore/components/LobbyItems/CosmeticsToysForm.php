<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class CosmeticsToysForm extends BaseFormComponent
{

    public function primary()
    {
        $buttons = ["Tags", "Cosmetics", "Toys"];
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $this->tags();
                    break;
                case 1:
                    break;
            }
        });
        $form->setTitle("Toys & Cosmetics");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }


    public function tags()
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            if (is_string($data["label"])) {

            }
        });
        $form->setTitle("Duels");
        $form->addInput("Enter Username of the player you wish to duel", "Username", "", "label");
        $this->p->sendForm($form);
    }
}