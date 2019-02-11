<?php

namespace Sakura\AE;

use pocketmine\Server;
use pocketmine\scheduler\Task;

class leTask extends Task{
    
    private $main;

    public function __construct(\Sakura\AE\leMain $core)
    {
    $this->main = $core;
    }
    
	public function onRun(int $currentTick)
	{
		if($this->main->core != null) {
		    $stayReq = $this->main->config->get("must_stay_for");
		    $ctime = $this->main->microtime_int();
			foreach($this->main->players as $name => $ptime)
			{
			$stayed = $ctime - $ptime; //check long the player is online (in seconds).
			if($stayed >= $stayReq) //then check if that is enough.
			{
				$hooman = $this->main->getServer()->getPlayerExact($name);
				if($hooman instanceof \pocketmine\Player) {
				    $this->main->core->data->addVal($hooman, "exp", $this->main->exp);
				    $hooman->sendMessage("§l§cS A K U R A ❯ You got ". $this->main->exp. " exp for being active!");
				    $this->main->players[$name] = $ctime;
				}
			}
			}
			$this->main->getServer()->broadcastMessage("§l§cS A K U R A ❯ §7All players that are online for ". (int) $stayReq / 60 . " minute(s) got free exp! Stay online!");
		}
	}
}

?>
