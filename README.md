## Telegram Downloader Bot

This bot helps download videos from different platforms to your telegram app, and you can share videos directly from links.

## Getting Started

Please before contributing read the following:

1. [Telegram bot api](https://core.telegram.org/bots)
2. Read the [php telegram sdk documentation](http://telegram-bot-sdk.readme.io)
3. [YouTube Documentation](https://developers.google.com/youtube/v3)

```bash
# install application dependencies
composer install

# copy example environment variables and change accordingly
cp .env.example .env
```

### Commands

```bash
# run to generate a telegram command class
php artisan make:telegram-command 

# run to generate download service for a supported platform
php artisan make:download-service
```

## Running On Local Machine

Make sure environment variable **TELEGRAM_BOT_TOKEN** is set. On every code change make sure to rerun command.

```bash
# this polls the telegram api for pending updates
php artisan poll:bot-updates
```

## Roadmap
 
 - [x] YouTube
 - [] Twitter (via a bot)

License
-------
This project is licensed under the MIT License - see the [LICENSE](https://github.com/heywhy/u_tube_telegram_bot/blob/master/LICENSE) file for details

Contributions
-------------
 Appreciate all contributions and suggestions which would make this a more useful application for all Nim Lang users. Feel free to fork this repo, create a pull request and it will be reviewed and merged!
