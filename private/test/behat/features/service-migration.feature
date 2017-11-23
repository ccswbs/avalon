Feature: Service content migration
	In order to verify migrations ran at all
	As a maintainer
	I need to see that service content came over with the correct field values

	Scenario: Check for fields in a service page
		When I go to "services/test-service-type-thursday"
		Then I should see "test service type - thursday"
		Then I should see "some service related text will go here"
		Then I should see "100.10"
