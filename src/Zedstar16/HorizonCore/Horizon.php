<?php

declare(strict_types=1);

namespace Zedstar16\HorizonCore;

use DirectoryIterator;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use SQLite3;
use Zedstar16\HorizonCore\cache\Cache;
use Zedstar16\HorizonCore\components\ChatHandler\ChatFilter;
use Zedstar16\HorizonCore\components\Crate\CrateItemShiftTask;
use Zedstar16\HorizonCore\components\DB;
use Zedstar16\HorizonCore\entities\Bosses\SnowmanBoss;
use Zedstar16\HorizonCore\entities\Bosses\ZombieBoss;
use Zedstar16\HorizonCore\entities\FireworksRocket;
use Zedstar16\HorizonCore\entities\FloatingItemEntity;
use Zedstar16\HorizonCore\entities\FloatingText;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenuHandler;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\API;
use Zedstar16\HorizonCore\listeners\EntityEventListener;
use Zedstar16\HorizonCore\listeners\InventoryEventListener;
use Zedstar16\HorizonCore\listeners\NetworkListener;
use Zedstar16\HorizonCore\listeners\PlayerEventListener;
use Zedstar16\HorizonCore\managers\CrateManager;
use Zedstar16\HorizonCore\managers\FFAManager;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\KitManager;
use Zedstar16\HorizonCore\tasks\async\AsyncDataLoader;
use Zedstar16\HorizonCore\tasks\Ticker;
use Zedstar16\ZedFun\entity\Fireworks;

class Horizon extends PluginBase implements Listener
{

    /** @var Horizon|null */
    private static $instance = null;

    public static $players = [];

    public static $currentbossbartick;

    public static $vanished = [];

    public const prefix = "§7[§6Horizon§cPE§7]";

    public $lines = 0;

    public $f = 0;

    const ENTITIES = [
        FloatingText::class,
        FireworksRocket::class,
        SnowmanBoss::class,
        ZombieBoss::class,
        FloatingItemEntity::class
    ];

    public function onEnable(): void
    {
        self::$instance = $this;
        self::$currentbossbartick = 0;
        API::load($this);
        $this->getServer()->getAsyncPool()->submitTask(new AsyncDataLoader());
        $this->getLogger()->info("Horizon Core Enabled");
        $this->initializeListeners();
        $this->initializePermissions();
        $this->registerTasks();
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        FFAManager::loadArenas();
        $this->removeCommands();
        $this->loadCommands(__DIR__ . "/commands");
        $this->getServer()->loadLevel("kit2");
        ItemFactory::registerItem(new Fireworks());
        Item::initCreativeItems();
        $i = 0;
        foreach (self::ENTITIES as $entity) {
            if (Entity::registerEntity($entity, true)) $i++;
        }
        $this->getLogger()->info("Total Lines of code: 12452");
        $this->getLogger()->notice("Registered $i/" . count(self::ENTITIES) . " custom entities successfully!");
        // CrateManager::registerCrates();
        $bad = ["cunt", "fuck", "shit", "nigga", "kys", "nigger"];
        $c = new ChatFilter("b1tch");
        $c->getCleanedMessage();
        var_dump($c->swear_found);
        var_dump($c->cansend);
        // print_r(KitManager::parseContents(FileManager::getYamlData("crate")["items"]));
        //  $this->getLines("plugins/HorizonCore/src/Zedstar16/HorizonCore/");
        // var_dump($this->lines);
        //var_dump($this->f);
        //$this->getScheduler()->scheduleRepeatingTask(new CrateItemShiftTask(), 1);
        $db = new SQLite3("plugins/data.db");
        $query = "CREATE TABLE IF NOT EXISTS customer(customerid INT, customername VARCHAR(255), address VARCHAR(16), phone INT, dateofbirth DATE);";
        $db->exec($query);
        DB::init();

    }

    public function getLines($path)
    {
        $i = new DirectoryIterator($path);
        foreach ($i as $item) {
            if (!$i->isDot()) {
                if ($i->isDir()) {
                    $this->getLines($i->getRealPath());
                } else {
                    $this->f++;
                    $this->lines += count(explode("\n", file_get_contents($item->getRealPath())));
                }
            }
        }
    }

    public function loadCommands($path)
    {
        $i = new DirectoryIterator($path);
        foreach ($i as $item) {
            if (!$i->isDot()) {
                if ($i->isDir()) {
                    $this->loadCommands($i->getRealPath());
                } else {
                    $path = explode(PHP_OS_FAMILY === "Linux" ? 'src/' : 'src\\', $item->getPath())[1];
                    $filename = $item->getFilename();
                    if (substr($filename, -4) === ".php") {
                        $class = str_replace("/", "\\", $path . "\\" . substr($filename, 0, -4));
                        $this->getServer()->getCommandMap()->register("horizon", new $class());
                    }
                }
            }
        }
    }

    private function removeCommands()
    {
        $commands = ["ban", "banlist", "pardon", "kick"];
        foreach ($commands as $command) {
            $commandMap = $this->getServer()->getCommandMap();
            $cmd = $commandMap->getCommand($command);
            if ($cmd === null) {
                return;
            }
            $cmd->setLabel("");
            $cmd->unregister($commandMap);
        }
    }

    public static function Config(): array
    {
        return [
            "scoreboardtick" => 5,
            "praczone" => [
                "max" => [
                    "x" => 222,
                    "y" => 215,
                    "z" => 262
                ],
                "min" => [
                    "x" => 211,
                    "y" => 205,
                    "z" => 251
                ]
            ],
            "kothcenter" => [
                "x" => 266,
                "y" => 26,
                "z" => 256
            ],
            "koth-duration" => 600,
            "ffalevels" => ["ffa1", "ffa2", "ffa3", "ffa4"]
        ];
    }

    /**
     * @return Horizon
     */
    public static function getInstance(): Horizon
    {
        return self::$instance;
    }


    private function initializePermissions()
    {
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setSkin($this->getServer()->getPlayer("zehriv")->getSkin());
            $p->sendSkin($this->getServer()->getOnlinePlayers());
        }
        $this->addPermissions([
            new Permission("horizon.base", "o", Permission::DEFAULT_OP),
            new Permission("horizon.gamemode", "", "op"),
            new Permission("horizon.gamemode.other", "", "op"),
            new Permission("horizon.report", "", "true"),
            new Permission("horizon.admin", "", "op"),
        ]);
    }

    /**
     * @param array $permissions
     */
    protected function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            PermissionManager::getInstance()->addPermission($permission);
        }
    }


    private function initializeListeners()
    {
        $this->registerListeners($this, [
            new PlayerEventListener($this->getServer()),
            new NetworkListener(),
            new EntityEventListener(),
            new InventoryEventListener()
        ]);
    }

    /**
     * @param Plugin $plugin
     * @param array $listeners
     */
    public function registerListeners(Plugin $plugin, array $listeners): void
    {
        foreach ($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $plugin);
        }
    }

    public function registerTasks()
    {
        $this->getScheduler()->scheduleRepeatingTask(new Ticker(), 20);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick): void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if (($player instanceof HorizonPlayer) && $player->getSession() !== null && $player->getSession()->getScoreboard() !== null) {
                    $player->getSession()->getScoreboard()->updateLine("Online", count($this->getServer()->getOnlinePlayers()));
                }
            }
        }), 5, 50);
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
            //    Server::getInstance()->getAsyncPool()->submitTask(new AsyncTopCalculator())
        }), 60 * 20);
    }

    private function initializeFiles()
    {
        $files = ["reportconfig.yml", "infoui.yml"];
        $path = $this->getDataFolder();
        foreach ($files as $file) {
            $filename = $path . $file;
            if (!(file_exists($filename))) {
                fopen($filename, "w+");
            }
        }
    }

    public static function getPlayer($username): ?HorizonPlayer
    {
        $player = Server::getInstance()->getPlayer($username);
        if ($player instanceof HorizonPlayer) {
            return $player;
        }
        return null;
    }

    public function onDisable()
    {
        Cache::write();
        $this->getLogger()->info("HorizonCore disabled");
    }
}