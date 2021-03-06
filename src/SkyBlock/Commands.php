<?php
namespace SkyBlock;

use SkyBlock\subcommand\AddHelperSubCommand;
use SkyBlock\subcommand\ClaimSubCommand;
use SkyBlock\subcommand\ClearSubCommand;
use SkyBlock\subcommand\DisposeSubCommand;
use SkyBlock\subcommand\GenerateSubCommand;
use SkyBlock\subcommand\HomeSubCommand;
use SkyBlock\subcommand\InfoSubCommand;
use SkyBlock\subcommand\ListSubCommand;
use SkyBlock\subcommand\ResetSubCommand;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\subcommand\SubCommand;
use pocketmine\Player;
use SkyBlock\subcommand\RemoveHelperSubCommand;
use SkyBlock\subcommand\AutoSubCommand;
use SkyBlock\subcommand\BiomeSubCommand;
use SkyBlock\subcommand\NameSubCommand;
use SkyBlock\subcommand\LockSubCommand;
use SkyBlock\subcommand\UnlockSubCommand;
use SkyBlock\subcommand\WarpSubCommand;

class Commands extends PluginCommand
{
	private $subCommands = [];

	/* @var SubCommand[] */
	private $commandObjects = [];

	public function __construct(SkyBlock $plugin) {
		parent::__construct("island", $plugin);
		$this->setAliases(["is"]);
		$this->setPermission("skyblock.command");
		$this->setDescription("Claim and manage your plots");

		$this->loadSubCommand(new ClaimSubCommand($plugin));
		$this->loadSubCommand(new GenerateSubCommand($plugin));
		$this->loadSubCommand(new ListSubCommand($plugin));
		$this->loadSubCommand(new InfoSubCommand($plugin));
		$this->loadSubCommand(new AddHelperSubCommand($plugin));
		$this->loadSubCommand(new RemoveHelperSubCommand($plugin));
		$this->loadSubCommand(new AutoSubCommand($plugin));
		$this->loadSubCommand(new ClearSubCommand($plugin));
		$this->loadSubCommand(new DisposeSubCommand($plugin));
		$this->loadSubCommand(new ResetSubCommand($plugin));
		$this->loadSubCommand(new BiomeSubCommand($plugin));
		$this->loadSubCommand(new HomeSubCommand($plugin));
		$this->loadSubCommand(new NameSubCommand($plugin));
		$this->loadSubCommand(new LockSubCommand($plugin));
		$this->loadSubCommand(new UnlockSubCommand($plugin));
		$this->loadSubCommand(new WarpSubCommand($plugin));
	}

	private function loadSubCommand(Subcommand $command) {
		$this->commandObjects[] = $command;
		$commandId = count($this->commandObjects) - 1;
		$this->subCommands[$command->getName()] = $commandId;
		foreach ($command->getAliases() as $alias) {
			$this->subCommands[$alias] = $commandId;
		}
	}

	public function execute(CommandSender $sender, $alias, array $args) {
		if (!isset($args[0])) {
			return $this->sendHelp($sender);
		}
		$subCommand = strtolower(array_shift($args));
		if (!isset($this->subCommands[$subCommand])) {
			return $this->sendHelp($sender);
		}
		$command = $this->commandObjects[$this->subCommands[$subCommand]];
		$canUse = $command->canUse($sender);
		if ($canUse) {
			if (!$command->execute($sender, $args)) {
				$sender->sendMessage(TextFormat::YELLOW."Usage: /is " . $command->getName() . " " . $command->getUsage());
			}
		} elseif (!($sender instanceof Player)) {
			$sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
		} else {
			$sender->sendMessage(TextFormat::RED . "You do not have permissions to run this command");
		}
		return true;
	}

	private function sendHelp(CommandSender $sender) {
		$sender->sendMessage("===========[SkyBlock commands]===========");
		foreach ($this->commandObjects as $command) {
			if ($command->canUse($sender)) {
				$sender->sendMessage(
					TextFormat::DARK_GREEN . "/is " . $command->getName() . " " . $command->getUsage() . ": " .
					TextFormat::WHITE . $command->getDescription()
				);
			}
		}
		return true;
	}
}
