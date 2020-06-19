<?php


namespace Zedstar16\HorizonCore\components;

use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\inventory\InvMenuInventory;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\KitManager;

class KitEditor
{

    /** @var Player */
    public $p;

    public function __construct(Player $player)
    {
        $this->p = $player;
        echo "HI";
        $this->primary();
    }

    public function primary()
    {
        $buttons = ["KitEditor", "FFA Editor"];
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            if ($data == 0) {
                $this->openDuelKitEditorUI();
            } elseif ($data == 1) {
                $this->openFFAKitEditorUI();
            }
        });
        $form->setTitle("Settings");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }

    public function openFFAKitEditorUI()
    {
        $buttons = ["BuildPvP", "NoDebuff", "Gapple", "Combo"];
        $form = new SimpleForm(function (Player $player, $data = null)  use($buttons) {
            if ($data === null) {
                return;
            }else{
                $this->openKitEditor(strtolower($buttons[$data]), Constants::KIT_FFA);
            }

        });
        $form->setTitle("Settings");
        $form->setContent("Select an option, Rearrange the kit then close the Inventory to save it");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);

    }

    public function openDuelKitEditorUI()
    {

    }

    public function openKitEditor($kit, $type){
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->readonly(false);
        $contents = $this->getEditorContents($kit, $type);
        $playerinventory = $this->p->getInventory()->getContents();
        $menu->setListener(function(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) use($playerinventory){
            if(in_array($itemClicked, $playerinventory) or in_array($itemClickedWith, $playerinventory)){
                return false;
            }
            return true;
        });
        $menu->setInventoryCloseListener(function (Player $player, InvMenuInventory $inventory)  {
            $inv = $player->getInventory()->getContents();
            foreach ($inv as $item) {
                if ($item->getNamedTag()->hasTag("chest")) {
                    $player->getInventory()->remove($item);
                }
            }
        });
        $menu->setName("Your $kit Inventory");
        $menu->getInventory()->setContents($this->getEditorContents($kit, $type));
        $menu->send($this->p);
    }

    private function getEditorContents($kit, $type): array
    {
        $kit = KitManager::getKit($this->p, $kit, $type);
        $armorcontent = $kit["armor"];
        $content = $kit["inventory"];
        $armor_slots = [
            0 => 38,
            1 => 39,
            2 => 40,
            3 => 41
        ];
        foreach ($armorcontent as $index => $armor) {
            $content[$armor_slots[$index]] = $armor;
        }
        for ($i = 45; $i <= 53; $i++) {
            if($i !== 48 && $i !== 49) {
                $content[$i] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName(" ");
            }
        }
        $content[48] = Item::get(Item::CONCRETE, 14)->setCustomName("Exit without Saving changes");
        $content[49] = Item::get(Item::CONCRETE, 5)->setCustomName("Save changes and exit");
        $content[45] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName(" ");
        $content[46] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName("Armor ->");
        $content[51] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName("<- Armor");
        $content[52] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName(" ");
        $content[53] = Item::get(Item::STAINED_GLASS_PANE, 15)->setCustomName(" ");

        foreach ($content as $item) {
            if ($item instanceof Item) {
                $nbt = $item->getNamedTag();
                $nbt->setString("chest", "chest");
                $item->setCompoundTag($nbt);
            }
        }
        return $content;
    }

}