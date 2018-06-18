Feature: URL Alias Patterns
	In order to add content to my site
	As an anonymous user
	I need to be able to see a node type with a specific path pattern.

	Scenario: Banner path pattern
		Given I am on "content/banner/test-banner"
		Then print current URL
		Then I should see "Test Banner"
