Feature: Breadcrumbs migration
	In order to verify news migrations ran at all
	As a maintainer
	I need to see that breadcrumbs for select use cases have correctly moved over

	Scenario: Page 2 in menu has proper breadcrumb
		Given I am on "page-2"
		Then print current URL
		And I should see "Page 2"
		And the response should contain "<nav class=\"breadcrumb\""
		And the response should contain "<a href=\"/\">Home</a>"

	Scenario: News in menu has proper breadcrumb
		Given I am on "news/2017/11/u-g-eco-friendly-flowerpots-blossom-us-canada"
		Then print current URL
		And I should see "U of G Eco-Friendly Flowerpots Blossom in U.S., Canada"
		And I should see "Home" in the "breadcrumb" region
		And I should see "News" in the "breadcrumb" region
		And I should not see "2017" in the "breadcrumb" region
		And I should not see "News News" in the "breadcrumb" region


	Scenario: Event in menu has proper breadcrumb
		Given I am on "events/2014/07/euismod-exputo"
		Then print current URL
		And I should see "Euismod Exputo"
		And I should see "Home" in the "breadcrumb" region
		And I should see "Events" in the "breadcrumb" region
		And I should not see "2014" in the "breadcrumb" region
		And I should not see "Events Events" in the "breadcrumb" region
