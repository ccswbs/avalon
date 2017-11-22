##
# migrate.sh
#
# Run D7 -> D8 migration.
#

# Wake up source site
terminus env:wake hjckrrh.dev
sleep 10 # give site a chance to wake up

# Re-install target site
terminus remote:drush ug-avalon.dev -- site-install minimal --config-dir=sites/default/config

# Run migration
terminus remote:drush ug-avalon.dev -- migrate-import --all </dev/null

# Update migration (second pass)
terminus remote:drush ug-avalon.dev -- migrate-import --update --all </dev/null

