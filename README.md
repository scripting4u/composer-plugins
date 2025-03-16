## Scripting4U composer plugin actions
This repository contains composer plugin actions. 
To perform certain actions that happen after `composer install` for instance or any other.

Currently registered events:
 - `ScriptEvent::POST_INSTALL_CMD = 'post-install-cmd'`
   - https://getcomposer.org/doc/articles/scripts.md#command-events

## Actions

`\Scripting4U\ComposerPlugins\CopyCsFiles`
After `composer install` has been ran, it copies the following set of files
 - `phpmd.xml`
 - `phpstan.neon`
 - `grumphp.yml`

This action also overwrites those files after a composer install.
NOTE : If this is not necessary we can change it to only copy them once, and have a local configuration per project.
This requires changes in the command to perform a check if the file exists in the root directory.

## Requires 
- `"php": "^8.0"`
- `"scripting4u/coding-standards": "^1.0.1"`

## Setup

add to composer file:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/scripting4u/composer-plugins"
        }
    ]
}
```

```php
composer require --dev "scripting4u/composer-plugins:^1.0.1"
```
