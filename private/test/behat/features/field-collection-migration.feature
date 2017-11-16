Feature: Field collection migration
	In order to verify field collections have been migrated
	As a maintainer
	I need to see that a field collection has moved from Drupal

	Scenario: Event heading
		Given I am on "events/2017/08/luctus"
		Then print current URL
		Then I should see "clathihuhacr"
		Then I should see "Autem fere neque os quibus tincidunt ullamcorper."

