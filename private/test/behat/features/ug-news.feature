Feature: UG News
	In order to add news to my site
	As an author
	I need views to list news articles

	Scenario: News teaser block
		Then I should see "News"
		Then I should see "Cui"
		Then I should see "Autem Quibus"
		Then I should not see "Nisl"
		
	Scenario: News listing view
		Given I am on "news"
		Then I should see "Cui"
		Then I should see "Autem Quibus"
		Then I should see "Nisl"

	Scenario: News listing view (filtered)
		Given I am on "news/category/31"
		Then I should see "Autem Quibus"
		Then I should not see "Cui"

	Scenario: News feed
		Given I am on "news/feed"
		Then the response should contain "Cui"
		Then the response should contain "Autem Quibus"

	Scenario: News feed (filtered)
		Given I am on "news/feed/31"
		Then the response should contain "Autem Quibus"
		Then the response should not contain "Cui"

