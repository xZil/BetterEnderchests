<?php

#  ____  ______ _______ _______ ______ _____  ______ _   _ _____  ______ _____   _____ _    _ ______  _____ _______ _____
# |  _ \|  ____|__   __|__   __|  ____|  __ \|  ____| \ | |  __ \|  ____|  __ \ / ____| |  | |  ____|/ ____|__   __/ ____|
# | |_) | |__     | |     | |  | |__  | |__) | |__  |  \| | |  | | |__  | |__) | |    | |__| | |__  | (___    | | | (___
# |  _ <|  __|    | |     | |  |  __| |  _  /|  __| | . ` | |  | |  __| |  _  /| |    |  __  |  __|  \___ \   | |  \___ \
# | |_) | |____   | |     | |  | |____| | \ \| |____| |\  | |__| | |____| | \ \| |____| |  | | |____ ____) |  | |  ____) |
# |____/|______|  |_|     |_|  |______|_|  \_\______|_| \_|_____/|______|_|  \_\\_____|_|  |_|______|_____/   |_| |_____/

declare(strict_types=1);

namespace zillion\betterenderchests\command;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;
use zillion\betterenderchests\Loader;

class SeeEnderchestCommand extends Command
{
    /** @var Inventory[] */
    public array $viewing = [];

    public function __construct()
    {
        parent::__construct("openenderchest", "Enderchest(Open Others) Root Command", null, ["oec", "seec", "viewec"]);
        $this->setPermission("enderchest.open.others");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;
        if (!$sender instanceof Player) return;
        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return;
        }

        $target = Server::getInstance()->getOfflinePlayer($args[0]);
        if ($target instanceof OfflinePlayer && !Server::getInstance()->hasOfflinePlayerData($args[0])) {
            $sender->sendMessage("§cPlayer not found.");
            return;
        }
        $chest = InvMenu::create(InvMenuTypeIds::TYPE_CHEST)
            ->setName($target->getName() . "'s Ender Chest")
            ->setInventoryCloseListener(function (Player $player, Inventory $inventory) {
                $this->viewing[$player->getName()]->setContents($inventory->getContents());
                if (Loader::getSingletonInstance()->getConfig()->get("enderchest")["play-sound-on-close"]) {
                    Loader::getSingletonInstance()->playSound($player, Loader::getSingletonInstance()->getConfig()->get("enderchest")["inventory-close-sound"], 50);
                }
                unset($this->viewing[$player->getName()]);
            });
        $this->viewing[$sender->getName()] = $target->getEnderInventory();
        $chest->getInventory()->setContents($target->getEnderInventory()->getContents());
        $chest->send($sender);
        if (Loader::getSingletonInstance()->getConfig()->get("enderchest")["play-sound-on-open"]) {
            Loader::getSingletonInstance()->playSound($sender, Loader::getSingletonInstance()->getConfig()->get("enderchest")["inventory-open-sound"], 50);
        }
    }
}