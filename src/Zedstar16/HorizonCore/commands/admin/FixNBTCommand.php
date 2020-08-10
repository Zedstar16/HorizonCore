<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\format\io\leveldb\LevelDB;
use pocketmine\level\LevelException;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Binary;

class FixNBTCommand extends Command
{


    public function __construct()
    {
        parent::__construct("nbt");
        $this->description = "spawn a boss";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            if (file_exists("worlds/$args[0]/level.dat")) {
                $rawLevelData = file_get_contents("worlds/$args[0]/level.dat");
                if ($rawLevelData === false or strlen($rawLevelData) <= 8) {
                    throw new LevelException("Truncated level.dat");
                }
                $nbt = new LittleEndianNBTStream();
                $levelData = $nbt->read(substr($rawLevelData, 8));
                if (!$levelData instanceof CompoundTag) {
                    throw new LevelException("Invalid level.dat");
                }
                $levelData->setString("generatorName", "default");
                $levelData->setString("generatorOptions", "");
                $nbt = new LittleEndianNBTStream();
                $buffer = $nbt->write($levelData);
                file_put_contents("worlds/$args[0]/level.dat", Binary::writeLInt(LevelDB::CURRENT_STORAGE_VERSION) . Binary::writeLInt(strlen($buffer)) . $buffer);

            } else $sender->sendMessage("Level not found");
        } else $sender->sendMessage("Specify a level");
    }

}