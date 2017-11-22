#!/bin/sh
#
# migrate-upgrade.sh	M. Brent Harp (brharp) 11/22/17
#
# Generates migration from D7 to D8.
#

# Environment variables
SITE="${SITE:-ug-avalon}"
ENV="${ENV:-dev}"
LEGACY_DB_KEY="${LEGACY_DB_KEY:-legacy}"
LEGACY_ROOT="${LEGACY_ROOT:-http://dev-hjckrrh.pantheonsite.io/}"

terminus remote:drush $SITE.$ENV -- migrate-upgrade --configure-only \
  --legacy-db-key=$LEGACY_DB_KEY --legacy-root=$LEGACY_ROOT

