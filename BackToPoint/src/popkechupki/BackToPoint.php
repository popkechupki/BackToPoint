<?php
namespace popkechupki;
/*
CosmoSunriseServer用ワープ系プラグインです。
このプラグインはpopke LISENCEに同意した上で使用してください。

Cosmo Sunrise Server Warp System.
開発開始年月日: 2016/07/09
最後の更新年月日: 2016/07/09
*/
use poketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use pocketmine\utils\{TextFormat, Config};
use pocketmine\command\{Command, CommandSender};

class BackToPoint extends PluginBase {

	function onEnable(){
		$this->getLogger()->info(TextFormat::GREEN."BackToPointを読み込みました".TextFormat::GOLD." By popkechupki");
		$this->getLogger()->info(TextFormat::RED."このプラグインはpopke LICENSEに同意した上で使用してください。");
		if (!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0740, true);
		$this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
	}

	function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$user = $sender->getName();
		if(!$sender instanceof Player) return $this->getLogger()->info("このプラグインのコマンドはゲーム内からのみ実行可能です。");
		switch (strtolower($label)){
			case 'setpoint':
				$x = $sender->x;
				$y = $sender->y;
				$z = $sender->z;
            	$this->data->set($user, array($x, $y, $z));
            	$this->data->save();
            	$sender->sendMessage("[BackToPoint]"."\n"."あなたのテレポート先を登録しました。 /pointで戻れます。");
				break;

			case 'point':
				if($this->data->exists($user)){
					$x = $this->data->get($user)[0];
					$y = $this->data->get($user)[1];
					$z = $this->data->get($user)[2];
					$pos = new Vector3($x, $y, $z);
					$sender->teleport($pos);
					$sender->sendMessage("[BackToPoint]"."\n"."設定された座標にテレポートしました。");
				}else{
					$sender->sendMessage("[BackToPoint]"."\n"."あなたのテレポート先の座標が設定されていません。 /setpointで設定してください。");
				}
				break;

			case 'delpoint':
				if($this->data->exists($user)){
					$this->data->remove($user);
					$this->data->save();
					$sender->sendMessage("[BackToPoint]"."\n"."あなたのテレポート先の座標を削除しました。");
				}else{
					$sender->sendMessage("[BackToPoint]"."\n"."あなたのテレポート先は設定されていません。 /setpointで設定してください。");
				}
				
				break;
		}
	}
	
}
