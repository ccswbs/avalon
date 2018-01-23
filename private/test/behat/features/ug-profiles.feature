Feature: UG Profiles
	In order to add profiles to my site
	As an author
	I need views to list people profiles

	Scenario: Profiles teaser block
		Given I am on "/"
		Then I should see "Meet our People"
		Then I should see "Profile 1"
		Then I should see "Profile 2"
		Then I should see "Profile 3"
		Then I should see "More people"
