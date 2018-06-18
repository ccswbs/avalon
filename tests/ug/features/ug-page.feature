Feature: UG Page
	In order to add content to my site
	As an author
	I need to add basic pages

	Scenario: Page listing view
		Given I am on "pages"
		Then print current URL
		Then I should see "Page 2"

	Scenario: Page listing view (filtered)
		Given I am on "pages/term/30"
		Then I should see "Page 2"
		Then I should not see "Filtered Listing Page"

	Scenario: Page feed
		Given I am on "pages/term/all/feed"
		Then the response should contain "Page 2"

	Scenario: Page feed (filtered)
		Given I am on "pages/term/30/feed"
		Then the response should contain "Page 2"
		Then the response should not contain "Filtered Listing Page"

