##
# migrate.sh
#
# Run D7 -> D8 migration.
#

terminus env:wake hjckrrh.dev
sleep 10 # give site a chance to wake up
terminus remote:drush ug-avalon.dev -- migrate-rollback --all </dev/null
terminus remote:drush ug-avalon.dev -- migrate-import --all </dev/null
terminus remote:drush ug-avalon.dev -- migrate-import --update --all </dev/null

