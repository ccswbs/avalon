Feature: Menu migration
	In order to verify menu links are migrated
	As a maintainer
	I need to see menu links on the home page

	Scenario: Home page
		Then print current URL
		Then I should see "Main menu"
		Then I should see "Page 2"
		Then the response should contain "<a href=\"/page-2\" data-drupal-link-system-path=\"node/2\">Page 2</a>"
		Then I should see "Quick Links"
		Then I should see "University of Guelph"
		Then the response should contain "<a href=\"https://www.uoguelph.ca/\" title=\"\">University of Guelph</a>"
		Then I should see "Intranet"
		Then the response should contain "<a href=\"https://intranet.uoguelph.ca/\" title=\"\">Intranet</a>"

