<?php

/**
 * Worlds | copy world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\exception\SourceNotExistException;
use surva\worlds\utils\exception\TargetExistException;
use surva\worlds\utils\FileUtils;
use surva\worlds\utils\Messages;

class CopyCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 2)) {
            return false;
        }

        $messages = new Messages($this->getWorlds(), $sender);

        if (!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($messages->getMessage("general.world.not_exist", ["name" => $args[0]]));

            return true;
        }

        $fromFolderName = $args[0];
        $toFolderName   = $args[1];

        if ($fromFolderName === $toFolderName) {
            $sender->sendMessage($messages->getMessage("copy.error_code.same_source_target"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath   = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        try {
            $res = FileUtils::copyRecursive($fromPath, $toPath);
        } catch (SourceNotExistException $e) {
            $sender->sendMessage($messages->getMessage("copy.error_code.source_not_exist"));

            return true;
        } catch (TargetExistException $e) {
            $sender->sendMessage($messages->getMessage("copy.error_code.target_exist"));

            return true;
        }

        if (!$res) {
            $sender->sendMessage($messages->getMessage("copy.error_code.copy_failed"));

            return true;
        }

        $sender->sendMessage(
            $messages->getMessage("copy.success", ["name" => $fromFolderName, "to" => $toFolderName])
        );

        return true;
    }
}
