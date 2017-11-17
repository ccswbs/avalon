Feature: Date migration
	In order to verify migrations ran at all
	As a maintainer
	I need to see that an event in Drupal with a date

	Scenario: Date
		Given I am on "events/2017/12/event-has-picture-carrot"
		Then print current URL
		Then I should see "Monday"
		Then I should see "December" 
		Then I should see "18th"
		Then I should see "2017"
		Then I should see "3:15PM"
		Then I should see "4:30PM"