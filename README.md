# GenderSystem
<p><b>GenderSystem</b> is a PocketMine-MP Plugin to Manage or Get player Gender!</p>

# Depend:
- ScoreHud

# How to get Player Gender?
<p>Use the class:</p>

```php
use Mrchlldev\GenderSystem;
```

<p>You can use this code to get the Player Gender:</p>

```php
GenderSystem::getInstance()->getPlayerGender($player);
```

# How to get tag and use the tag in ScoreHud plugin?
<p>You can use this tag:</p>

```yaml
gendersystem.gender
```

## Other:
<p>You can setting the text Male or Female in config.yml, see this code:</p>

```yaml
prefix: "§7(§2GenderSystem§7) §r"

gender:
  male: "Male"
  female: "Female"
```
