<?php

#  ____  ______ _______ _______ ______ _____  ______ _   _ _____  ______ _____   _____ _    _ ______  _____ _______ _____
# |  _ \|  ____|__   __|__   __|  ____|  __ \|  ____| \ | |  __ \|  ____|  __ \ / ____| |  | |  ____|/ ____|__   __/ ____|
# | |_) | |__     | |     | |  | |__  | |__) | |__  |  \| | |  | | |__  | |__) | |    | |__| | |__  | (___    | | | (___
# |  _ <|  __|    | |     | |  |  __| |  _  /|  __| | . ` | |  | |  __| |  _  /| |    |  __  |  __|  \___ \   | |  \___ \
# | |_) | |____   | |     | |  | |____| | \ \| |____| |\  | |__| | |____| | \ \| |____| |  | | |____ ____) |  | |  ____) |
# |____/|______|  |_|     |_|  |______|_|  \_\______|_| \_|_____/|______|_|  \_\\_____|_|  |_|______|_____/   |_| |_____/

declare(strict_types=1);

namespace zillion\betterenderchests;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use zillion\betterenderchests\command\ClearEnderchestCommand;
use zillion\betterenderchests\command\EnderchestCommand;
use zillion\betterenderchests\command\SeeEnderchestCommand;

class Loader extends PluginBase implements Listener
{
    use SingletonTrait {
        setInstance as private;
        getInstance as private getSingletonInstance;
    }

    public const VERSION = 1;

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());

        $this->saveDefaultConfig();

        $this->registerCommands();

        if ($this->getConfig()->get("version") !== self::VERSION) {
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.old.yml");
            $this->saveDefaultConfig();
            $this->reloadConfig();
        }

        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        self::setInstance($this);
    }

    public function registerCommands(): void
    {
        $map = $this->getServer()->getCommandMap();
        foreach (array(new ClearEnderchestCommand(), new EnderchestCommand(), new SeeEnderchestCommand()) as $cmd) {
            $map->register("betterenderchests", $cmd);
        }
    }

    public function playSound(Player $player, string $sound, $volume = 1, $pitch = 1)
    {
        $packet = new PlaySoundPacket();
        $packet->x = $player->getPosition()->getFloorX();
        $packet->y = $player->getPosition()->getFloorY();
        $packet->z = $player->getPosition()->getFloorZ();
        $packet->soundName = $sound;
        $packet->volume = $volume;
        $packet->pitch = $pitch;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        if (!$event->isCancelled()) {
            if ($event->getBlock()->getId() === VanillaBlocks::ENDER_CHEST()->getId()) {
                if ($this->getConfig()->get("enderchest")["block-disabled"]) {
                    $event->cancel();
                    $event->getPlayer()->sendMessage(TextFormat::RED . "Enderchests are Disabled. If You Believe this to be an Issue Please Aware a Server Administrator.");
                }
            }
        }
    }

    public static function getSingletonInstance(): Loader
    {
        return self::getInstance();
    }
}