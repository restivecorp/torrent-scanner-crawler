# Torrent Scanner Crawler
PHP crwaler/scraping/parser to search and download torrents. Optimized for DivxTotal

## What is?
Allows the following actions
 - You can show all contents about that serie and download all torrent links
 - Get last episode from yotu serie
 - Monitorice TV Serie and download when a new episode is available

## Required
 * curl php7.0 php-fpm php-curl php-dom php-xml sqlite3 php-sqlite3

## Install
```
	$ cd /var/www/
	$ git clone https://github.com/ruboweb/torrent-scanner-crawler.git
	$ mv torrent-scanner-crawler.git tsc
```

## Configure Paths
Edit this funtions to set correct paths in 'tsc/common.php' file:
 * function get_download_output_directory() { ... } // Path to download torrents files
 * function get_log_path() { ... } // Path to write log
 * function get_db_path() { ... } // DataBase path

## Configure DB
```
	$ cd /var/www/tsc/db
	mv torrent.db.empty torrent.db

	sudo mkdir /var/ts
	sudo mv torrent.db /var/ts/torrent.db
	sudo chmod 777 /var/ts/
	sudo chmod 777 /var/ts/*
```

For automatic dowloads yo can configure the script into cron trask.

## Usage
Type next command to show help about:
```
  $ php scanner.php
```
