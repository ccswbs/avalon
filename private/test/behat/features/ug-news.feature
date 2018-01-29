Feature: UG News
	In order to add news to my site
	As an author
	I need views to list news articles

	Scenario: News teaser block
		Given I am on "/"
		Then I should see "News"
		Then I should see "Cui"
		Then I should see "Autem Quibus"
		Then I should not see "Persto Ratis"

	Scenario: News listing view
		Given I am on "news"
		Then I should see "Cui"
		Then I should see "Autem Quibus"
		Then I should see "Nisl"

	Scenario: News listing view (filtered)
		Given I am on "news/term/31"
		Then I should see "News related to Test News Category"
		Then I should see "Autem Quibus"

	Scenario: News feed
		Given I am on "news/feed"
		Then the response should contain "Cui Title"
		Then the response should contain "Autem Quibus"

	Scenario: News feed (filtered)
		Given I am on "news/feed/31"
		Then the response should contain "Autem Quibus"
		Then the response should not contain "Cui Title"

