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
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use zillion\betterenderchests\Loader;

class EnderchestCommand extends Command
{
    public function __construct()
    {
        parent::__construct("enderchest", "Enderchest Root Command", null, ["ec", "enderc"]);
        $this->setPermission("enderchest.open");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;
        if (!$sender instanceof Player) return;
        if (Loader::getSingletonInstance()->getConfig()->get("enderchest")["enabled"]) {
            $chest = InvMenu::create(InvMenuTypeIds::TYPE_CHEST)
                ->setName($sender->getName() . "'s Ender Chest")
                ->setInventoryCloseListener(function (Player $player, Inventory $inventory) {
                    $player->getEnderInventory()->setContents($inventory->getContents());
                    if (Loader::getSingletonInstance()->getConfig()->get("enderchest")["play-sound-on-close"]) {
                        Loader::getSingletonInstance()->playSound($player, Loader::getSingletonInstance()->getConfig()->get("enderchest")["inventory-close-sound"], 50);
                    }
                });
            $chest->getInventory()->setContents($sender->getEnderInventory()->getContents());
            $chest->send($sender);
            if (Loader::getSingletonInstance()->getConfig()->get("enderchest")["play-sound-on-open"]) {
                Loader::getSingletonInstance()->playSound($sender, Loader::getSingletonInstance()->getConfig()->get("enderchest")["inventory-open-sound"], 50);
            }
        } else $sender->sendMessage(TextFormat::RED . "Enderchests are Disabled. If You Believe this to be an Issue Please Aware a Server Administrator.");
    }
}