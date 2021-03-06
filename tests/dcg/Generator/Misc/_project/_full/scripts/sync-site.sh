#!/usr/bin/env bash

# Synchronize local site instance with remote one.

set -e

ROOT_DIR=$(dirname "$(readlink -f "$0")")/..

function label {
  echo -e "\n\e[1;47;44m $* \e[0m"
}

function local_drush {
 "$ROOT_DIR"/vendor/drush/drush/drush --root "$ROOT_DIR"/web/ "$@"
}

SOURCE_ENVIRONMENT=$1

# Source environment from which we copy the database and files.
if [[ -z $SOURCE_ENVIRONMENT ]]; then
  read -r -p "Source environment: " SOURCE_ENVIRONMENT
fi

label 'Empty current database'
local_drush sql:drop -y

label "Import database from $SOURCE_ENVIRONMENT"
# @DCG gzip does not make much sense for small databases.
local_drush "@$SOURCE_ENVIRONMENT" sql:dump --gzip | gunzip | local_drush sql:cli

label "Synchronize files with $SOURCE_ENVIRONMENT"
# @DCG To save time and disk space consider using Stage File Proxy module.
TARGET_DIR=$(realpath "$ROOT_DIR"/web/sites/default/files)
local_drush core:rsync -y "@$SOURCE_ENVIRONMENT:sites/default/files/" "$TARGET_DIR" || true

label 'Apply DB updates'
local_drush updatedb -y

label 'Import configuration'
local_drush config:import -y

label 'Check config status'
local_drush config:status

label 'Rebuild caches'
local_drush cache:rebuild

label 'Run CRON hooks'
local_drush core:cron

label 'Delete log records'
local_drush watchdog:delete all -y

label 'Warm cache'
URL=$(local_drush core:status --field=uri)
if [[ $URL == *"default"* ]]; then
  echo -e "\n\e[91mURL is not set. Skipping.\e[0m" >&2
else
  curl -s -o /dev/null -w "URL: %{url_effective}\nStatus code: %{http_code}\nTime total: %{time_total} sec.\n $URL"
fi

label 'Check site status'
local_drush core:status
