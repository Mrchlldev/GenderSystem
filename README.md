# GenderSystem
<p><b>GenderSystem</b> is a PocketMine-MP Plugin to Manage or Get player Gender!</p>

# How to get Player Gender?
<p>Use the class:</p>

```php
use Mrchlldev\GenderSystem;
```

<p>You can use this to get the Player Gender:</p>

```php
GenderSystem::getInstance()->getPlayerGender($player);
```

## Other:
<p>You can setting the text Male or Female in config.yml, see this code:</p>

```yaml
prefix: "§7(§2GenderSystem§7) §r"

gender:
  male: "Male"
  female: "Female"
```
