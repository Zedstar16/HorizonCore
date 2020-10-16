<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\components\KitEditor;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class SettingsForm extends BaseFormComponent
{

    public function primary()
    {
        $buttons = ["BuildPvP Blocks", "KitEditor", "Chat Filter"];
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    new KitEditor($this->p);
                    break;
                case 1:
                    $this->chatFilter();
                    break;
            }
        });
        $form->setTitle("Settings");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
    }

    public function chatFilter()
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            var_dump($data);
        });
        $form->setTitle("Settings");
        $form->addToggle("View explicit chat", false, "label");
        $this->p->sendForm($form);
    }

    public function buildpvpblocks()
    {

        $buttons = [];
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if (isset($buttons[$data])) {
                $id = $buttons[$data]["id"];
                $damage = $buttons[$data]["damage"];
            }
        });
        foreach ($buttons as $button) {

            $form->addButton($button["name"]);
        }
    }


}