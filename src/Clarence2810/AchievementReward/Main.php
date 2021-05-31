<?php

namespace Clarence2810\AchievementReward;

use pocketmine\{
	Player,
	command\ConsoleCommandSender,
	event\Listener,
	event\player\PlayerAchievementAwardedEvent,
	item\Item,
	item\enchantment\Enchantment,
	item\enchantment\EnchantmentInstance,
	plugin\PluginBase,
	utils\Textformat as C,
};
class Main extends PluginBase implements Listener
{
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onAchievement(PlayerAchievementAwardedEvent $event):void{
		$player = $event->getPlayer();
		$ach = $event->getAchievement();
		if($this->getConfig()->exists($ach)){
			if(isset($this->getConfig()->get($ach)["item"])) foreach($this->getConfig()->get($ach)["item"] as $data){
				$item = Item::get($data["id"], $data["meta"] ?? 0, $data["count"] ?? 1);
				if(isset($data["name"])) $item->setCustomName(C::RESET . C::WHITE . str_replace("&", C::ESCAPE, $data["name"]));
				$lores = [];
				if(isset($data["lore"])) foreach($data["lore"] as $lore){
					$lores[] = C::RESET . C::WHITE . str_replace("&", C::ESCAPE, $lore);
				}
				if(!empty($lores)) $item->setLore($lores);
				if(isset($data["enchantment"])) foreach($data["enchantment"] as $enchantment) $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantment["id"]), $enchantment["level"] ?? 1));
				$player->getInventory()->addItem($item);
			}
			if(isset($this->getConfig()->get($ach)["command"])) foreach($this->getConfig()->get($ach)["command"] as $command) $this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace(["{player}", "&"], [$player->getName(), C::ESCAPE], $command));
		}
	}
}
	