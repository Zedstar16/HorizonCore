<?php


namespace Zedstar16\HorizonCore\components;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\inventory\InvMenuInventory;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenu;
use Zedstar16\HorizonCore\managers\KitManager;
use Zedstar16\HorizonCore\utils\Utils;

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
        $form->setTitle("SettingsForm");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }

    public function openFFAKitEditorUI()
    {
        $buttons = ["BuildPvP", "NoDebuff", "Gapple", "Combo"];
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if ($data === null) {
                return;
            } else {
                $this->openKitEditor(strtolower($buttons[$data]), Constants::KIT_FFA);
            }
        });
        $form->setTitle("SettingsForm");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }

    public function openDuelKitEditorUI()
    {
        $buttons = ["NoDebuff", "Gapple", "Combo", "BuildPvP", "Diamond", "Archer", "Spleef"];
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if ($data === null) {
                return;
            } else {
                $this->openKitEditor(strtolower($buttons[$data]), Constants::KIT_DUELS);
            }
        });
        $form->setTitle("SettingsForm");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }

    public function openNormalKitEditorUI()
    {
        $buttons = [];
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if ($data === null) {
                return;
            } else {
                $this->openKitEditor(strtolower($buttons[$data]), Constants::KIT_DUELS);
            }
        });
        $form->setTitle("SettingsForm");
        $form->setContent("Select an option");
        foreach ($buttons as $button) {
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }

    public function openKitEditor($kit, $type)
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $playerinventory = $this->p->getInventory()->getContents();
        $menu->setListener(function (Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) use ($playerinventory, $menu, $kit, $type) {
            if (!in_array($itemClicked, $menu->getInventory()->getContents()) or in_array($itemClickedWith, $playerinventory)) {
                echo 1;
                return false;
            }
            if ($action->getInventory() === $player->getInventory()) {
                echo 2;
                return false;
            }
            if ($itemClicked->getId() == ItemIds::STAINED_GLASS_PANE) {
                echo 3;
                return false;
            }
            if ($itemClicked->getId() == ItemIds::CONCRETE) {
                if ($itemClicked->getDamage() == 14) {
                    $player->sendMessage("§cExit without saving changes to kit inventory");
                    $player->removeAllWindows();
                } elseif ($itemClicked->getDamage() == 5) {
                    $this->saveContents($player, $kit, $type, $action->getInventory()->getContents());
                    $player->sendMessage("§aSuccessfully saved changes to kit");
                    $player->removeAllWindows();
                } else {
                    $menu->getInventory()->setContents($this->getEditorContents($kit, $type, true));
                }
                return false;
            }
            return true;
        });
        $menu->setInventoryCloseListener(function (Player $player, InvMenuInventory $inventory) {
            $inv = $player->getInventory()->getContents();
            foreach ($inv as $item) {
                if ($item->getNamedTag()->hasTag("chest")) {
                    $player->getInventory()->remove($item);
                }
            }
        });
        $typed = Utils::getNamed(Constants::$kit[$type]);
        $named = Utils::getNamed($kit);
        $menu->setName("Your §d$typed §c$named §rInventory");
        $menu->getInventory()->setContents($this->getEditorContents($kit, $type));
        $menu->send($this->p);
    }

    public function saveContents($player, $kit, $type, $contents)
    {
        $data = [];
        foreach ($contents as $key => $item) {
            if ($item->getNamedTagEntry("chest") == null) {
                unset($data[$key][array_keys($data[$key], $item)[0]]);
            }
        }
        for ($i = 0; $i < 36; $i++) {
            $data["inventory"][$i] = KitManager::indexItem($contents[$i] ?? Item::get(0));
        }
        for ($i = 0; $i <= 3; $i++) {
            $key = $i + 38;
            $data["armor"][$i] = KitManager::indexItem($contents[$key] ?? Item::get(0));
        }
        KitManager::saveCustomKit($player, $kit, $type, $data);
    }

    private function getEditorContents($kit, $type, $default = false): array
    {
        $kit = KitManager::getKit($this->p, $kit, $type, $default);
        $armorcontent = $kit["armor"];
        $content = $kit["inventory"];
        $armor_slots = [
            0 => 38,
            1 => 39,
            2 => 40,
            3 => 41
        ];
        for ($i = 36; $i <= 53; $i++) {
            $item = Item::get(Item::STAINED_GLASS_PANE, 15);
            $item->setCustomName(" ")
                ->getNamedTag()->setString("pane", "pane");
            $content[$i] = $item;

        }
        foreach ($armorcontent as $index => $armor) {
            $content[$armor_slots[$index]] = $armor;
        }
        $content[48] = Item::get(Item::CONCRETE, 14)->setCustomName("§r§cExit without saving changes");
        $content[49] = Item::get(Item::CONCRETE, 1)->setCustomName("§r§6Reset to default");
        $content[50] = Item::get(Item::CONCRETE, 5)->setCustomName("§r§aSave changes and exit");
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