<?php
namespace popkechupki;
/*
CosmoSunriseServer用ワープ系プラグインです。
このプラグインはpopke LISENCEに同意した上で使用してください。

Cosmo Sunrise Server Warp System.
開発開始年月日: 2016/07/09
最後の更新年月日: 2016/11/09
*/

use poketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use pocketmine\utils\{TextFormat as TF, Config};
use pocketmine\command\{Command, CommandSender};

class BackToPoint extends PluginBase{

	function onEnable(){
		$this->getLogger()->info("\n".TF::GREEN."BackToPointを読み込みました。"."\n".TF::RED."このプラグインはpopke LICENSEに同意した上で使用してください。");
		if (!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0740, true);
		$this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
	}

	function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$user = $sender->getName();
		if(!$sender instanceof Player) return $this->getLogger()->info("このプラグインのコマンドはゲーム内からのみ実行可能です。");
		switch (strtolower($label)){
			case 'setpoint':
				if(isset($args[1])){
					if($this->data->exists($args[1])){
						$sender->sendMessage("[BackToPoint]"."\n"."PointName: $args[1] はすでに登録されています。");
					}else{
						$x = $sender->x;
						$y = $sender->y;
						$z = $sender->z;
						$this->data->set($args[1], array($user, $x, $y, $z));
						$this->data->save();
						$sender->sendMessage("[BackToPoint]"."\n"."テレポート先( PointName: $args[1] )を登録しました。\n /point <PointName>で戻れます。");
					}
				}else{
					$sender->sendMessage("[BackToPoint]"."\n"."/setpoint <PointName>");
				}
				break;

			case 'point':
				if(isset($args[1])){
					if($this->data->exists($args[1])){
						$x = $this->data->get($args[1])[1];
						$y = $this->data->get($args[1])[2];
						$z = $this->data->get($args[1])[3];
						$pos = new Vector3($x, $y, $z);
						$sender->teleport($pos);
						$sender->sendMessage("[BackToPoint]"."\n"."PointName: $args[1] にテレポートしました。");
					}else{
						$sender->sendMessage("[BackToPoint]"."\n"."/point $args[1]"."\n"."上記のPointNameは登録されていません。");
					}
				}else{
					$sender->sendMessage("[BackToPoint]"."\n"."/point <PointName>");
				}
				break;

			case 'delpoint':
				if(isset($args[1])){
					if($this->data->exists($args[1])){
						$owner = $this->data->get($args[1])[0];
						if($user === $owner){
							$this->data->remove($args[1]);
							$this->data->save();
							$sender->sendMessage("[BackToPoint]"."\n"."PointName: $args[1] の座標を削除しました。");
						}else{
							$sender->sendMessage("[BackToPoint]"."\n"."PointName: $args[1] は $owner さん以外は削除できません。");
						}
					}else{
						$sender->sendMessage("[BackToPoint]"."\n"."/delpoint $args[1]"."\n"."上記のPointNameは登録されていません。");
					}
				}else{
					$sender->sendMessage("[BackToPoint]"."\n"."/delpoint <PointName>");
				}
				break;
		}
	}

}
