Feature: News migration
	In order to verify news migrations ran at all
	As a maintainer
	I need to see that an article has moved from Drupal

	Scenario: News article
		Given I am on "news/2017/11/u-g-eco-friendly-flowerpots-blossom-us-canada"
		Then print current URL
		Then I should see "U of G Eco-Friendly Flowerpots Blossom in U.S., Canada"
		Then I should see "Written by University of Guelph"
		Then I should see "University of Guelph gryphon statue"
		Then the response should contain "<img src=\"/sites/default/files/UofG-Math-Stats-Stock-Web-FullSize-034%20copy.jpg\" width=\"1140\" height=\"291\" alt=\"University of Guelph gryphon statue\" typeof=\"foaf:Image\" />"
		Then I should see "Affordable, eco-friendly flowerpots "


