##
# migrate.sh
#
# Run D7 -> D8 migration.
#

# Wake up source site
terminus env:wake hjckrrh.dev
sleep 10 # give site a chance to wake up

# Set target site to SFTP mode (required for install)
terminus connection:set ug-avalon.dev sftp

# Re-install target site
terminus remote:drush ug-avalon.dev -- site-install -y minimal --config-dir=sites/default/config

# Run migration
terminus remote:drush ug-avalon.dev -- migrate-import --all </dev/null

# Update migration (second pass)
terminus remote:drush ug-avalon.dev -- migrate-import --update --all </dev/null

# Flip target site back to git mode
terminus connection:set ug-avalon.dev git

