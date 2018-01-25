#!/bin/bash

set -euo pipefail

# Migrate Arts

SITE=ug-arts
ENV=dev
LEGACY_SITE=arts-legacy
LEGACY_ENV=dev
LEGACY_DB_URL=mysql://pantheon:6571a38888934b41859dc105f07f2bea@dbserver.dev.fd24ccd0-8650-4fbd-8cb4-6971f86e0b89.drush.in:17189
LEGACY_DB_KEY=legacy
LEGACY_ROOT=http://dev-arts-legacy.pantheonsite.io
terminus drush $SITE.$ENV -- site-install minimal -y
terminus drush $SITE.$ENV -- en -y migrate_upgrade,migrate_tools,migrate_plus
terminus env:wake $LEGACY_SITE.$LEGACY_ENV
terminus drush $SITE.$ENV -- migrate-upgrade --configure-only --legacy-db-key=$LEGACY_DB_KEY --legacy-root=$LEGACY_ROOT
terminus drush $SITE.$ENV -- migrate-status
terminus drush $SITE.$ENV -- uli


