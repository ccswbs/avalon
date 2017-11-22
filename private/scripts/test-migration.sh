#!/bin/bash
# 
# test-migration.sh - Test migration
#

composer test -- --format junit --out private/test/behat/results

