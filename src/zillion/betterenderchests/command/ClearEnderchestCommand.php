<?php

#  ____  ______ _______ _______ ______ _____  ______ _   _ _____  ______ _____   _____ _    _ ______  _____ _______ _____
# |  _ \|  ____|__   __|__   __|  ____|  __ \|  ____| \ | |  __ \|  ____|  __ \ / ____| |  | |  ____|/ ____|__   __/ ____|
# | |_) | |__     | |     | |  | |__  | |__) | |__  |  \| | |  | | |__  | |__) | |    | |__| | |__  | (___    | | | (___
# |  _ <|  __|    | |     | |  |  __| |  _  /|  __| | . ` | |  | |  __| |  _  /| |    |  __  |  __|  \___ \   | |  \___ \
# | |_) | |____   | |     | |  | |____| | \ \| |____| |\  | |__| | |____| | \ \| |____| |  | | |____ ____) |  | |  ____) |
# |____/|______|  |_|     |_|  |______|_|  \_\______|_| \_|_____/|______|_|  \_\\_____|_|  |_|______|_____/   |_| |_____/

declare(strict_types=1);

namespace zillion\betterenderchests\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\OfflinePlayer;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ClearEnderchestCommand extends Command
{
    public function __construct()
    {
        parent::__construct("clearenderchest", "Enderchest(Clear) Root Command", null, ["clearec", "cec"]);
        $this->setPermission("enderchest.open");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;
        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return;
        }
        $target = Server::getInstance()->getOfflinePlayer($args[0]);
        if ($target instanceof OfflinePlayer && !Server::getInstance()->hasOfflinePlayerData($args[0])) {
            $sender->sendMessage(TextFormat::RED . "Player not found.");
            return;
        }
        $target->getEnderInventory()->setContents([]);
        $sender->sendMessage(TextFormat::GREEN . "Successfully Cleared {$target->getName()}'s Enderchest.");
    }
}