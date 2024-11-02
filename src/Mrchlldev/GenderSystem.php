<?php

namespace Mrchlldev;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use jojoe77777\FormAPI\SimpleForm;
use Ifera\Scorehud\Scorehud;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\TagsResolveEvent;;

class GenderSystem extends PluginBase implements Listener {
  use SingletonTrait;

    public Config $data;
    public Config $config;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        $this->data = new Config($this->getDataFolder() . "data-gender.yml", Config::YAML);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if(!$player->isOnline()) continue;
                (new PlayerTagUpdateEvent($player, new ScoreTag("gendersystem.gender", $this->getPlayerGender($player))))->call();
            }
        }), 20);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() === "seegender"){
            if(!isset($args[0])){
                $sender->sendMessage("§cUsage: §f/seegender <player>");
                return false;
            }
            $target = $this->getServer()->getPlayerByPrefix(strtolower($args[0]));
            if(!$target instanceof Player){
                $sender->sendMessage("§cPlayer by name: §e" . $args[0] . " §cnot found!");
                return false!
            }
            $gender = $this->getPlayerGender($target);
            $sender->sendMessage("§aGender by name: §b" . $target . "\n§aIs a §b" . $gender);
        }
    }

    public function onTagsResolve(TagsResolveEvent $event): void {
        $tag = $event->getTag();
        $player = $event->getPlayer();
        $tags = explode('.', $tag->getName(), 2);
        $value = "";
        if ($tags[0] !== 'gendersystem' || count($tags) < 2) return;
        switch($tags[1]){
            case "gender":
                $value = $this->getPlayerGender($player);
            break;
        }
        $tag->setValue($value);
    }

    public function getPluginPrefix(): string {
        return $this->config->get("prefix");
    }

    public function setGender(Player $player, string $gender): void {
        $data = $this->data->getNested("player", []);
        $new = [
            "gender" => $gender,
            "date" => date("d-m-Y")
        ];
        $this->data->setNested("player." . $player->getName(), $new);
        $this->data->save();
        $this->data->reload();
    }

    public function hasGender(Player $player): bool {
        return $this->data->getNested("player." . $player->getName()) ?? false;
    }

    public function getPlayerGender(Player $player) {
        return $this->data->getNested("player." . $player->getName() . ".gender") ?? "Unknown";
    }

    public function getDateGender(Player $player) {
        return $this->data->getNested("player." . $player->getName() . ".date") ?? "Unknown";
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if($this->data->getNested("player." . $player->getName()) === null){
            $player->sendMessage($this->config->get("prefix") . "§aHello! Please select your gender!");
            $player->sendTitle("§aHello!",  "§ePlease select your gender!");
            $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
                $this->sendGenderMenu($player);
            }), 40);
        }
    }

    public function sendGenderMenu(Player $player, string $text = ""): void {
        if($this->hasGender($player)){
            $player-sendMessage("§aYou have gender: " . $this->getPlayerGender($player));
            return;
        }
        $form = new SimpleForm(function(Player $player, $data){
            if($data === null) $this->sendGenderMenu($player, "§cYou must select your gender!");
            switch($data){
                case 0:
                    $this->setGender($player, $this->config->getNested("gender.male", "Male"));
                    $player->sendMessage("§aSuccesfully set your gender to: §b" . $this->config->getNested("gender.male", "Male"));
                    break;
                case 1:
                    $this->setGender($player, $this->config->getNested("gender.female", "Female"));
                    $player->sendMessage("§aSuccesfully set your gender to: §b" . $this->config->getNested("gender.female", "Female"));
                    break;
            }
        });
        $form->setTitle("Gender");
        $form->setContent($text . "\nSelect your gender!");
        $form->addButton($this->config->getNested("gender.male", "Male"));
        $form->addButton($this->config->getNested("gender.female", "Female"));
        $player->sendForm($form);
    }
}