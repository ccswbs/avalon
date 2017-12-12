Feature: Block migration
	In order to verify blocks are migrated
	As a maintainer
	I need to see block content

	Scenario: Check that blocks were imported
		When I go to "/"
		Then print current URL
		Then I should see "Page 2.1"
		Then I should see "sample event"
		Then I should see "test service"
		Then I should see "Powered by Drupal"
		


