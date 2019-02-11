<?php

namespace Sakura\AE;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\config;

class leMain extends PluginBase implements Listener{

	public $random = false, $exp, $core, $config, $players = [];
	
    	public function onLoad()
    	{
		$this->getLogger()->info("§eLoading......");
    	}
	
	public function onEnable()
	{
		$this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$this->config->getAll();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->hasValidInterval();
		$this->core = Server::getInstance()->getPluginManager()->getPlugin("CoreX2");
	}
	
	public function onDisable()
	{
		$this->getLogger()->info("§6AutoXP§c has been disabled!");   
	}

	private function hasValidInterval() : bool
	{
		if(!is_integer($this->config->get("must_stay_for"))){
			$this->getLogger()->critical("Invalid interval in the config! Plugin Disabling...");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return false;
		}
		elseif(is_integer($this->config->get("must_stay_for"))){
			
			$this->getScheduler()->scheduleRepeatingTask(new leTask($this), 1200);
			if($this->config->getNested("experience.type") == "random")
			{
				$this->random = true;
				$xp = explode(":", $this->config->getNested("experience.amount_random"));
				$this->exp = mt_rand(intval($xp[0]), intval($xp[1]));
			} else {
				$this->exp = $this->config->getNested("experience.amount_fixed");
			}
			
			$this->getLogger()->Info("§6AutoEXP§a has been enabled!");
			$this->getLogger()->Info("§6---------------------------");
			$this->getLogger()->Info("§6Random exp? ". ($this->random ? "true" : "false"));
			$this->getLogger()->Info("§6Exp: ". $this->exp);
			$this->getLogger()->Info("§6Must stay for: ". $this->config->get("must_stay_for"));
			$this->getLogger()->Info("§6---------------------------");
			return true;
		}
		return false;
	}
	
	public function onLine(PlayerJoinEvent $event) : void
	{
		$player = $event->getPlayer()->getName();
		if(!in_array($player, $this->players))
		{
			$this->players[$player] = $this->microtime_int();
		}
	}
	
	public function offLine(PlayerQuitEvent $event) : void
	{
		$player = $event->getPlayer()->getName();
		if(in_array($player, $this->players))
		{
			unset($this->players[$player]);
		}
	}
	
	public function microtime_int()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((int)$usec + (int)$sec);
    }
}
?>
