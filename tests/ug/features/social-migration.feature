Feature: Social media migration
	In order to verify social media migrations ran at all
	As a maintainer
	I need to see that a social media account has moved from Drupal

	Scenario: Damnum
		Given I am on "/content/social/damnum"
		Then print current URL
		Then I should see "Damnum"
		Then I should see "Flickr"
		Then I should see "http://dev-hjckrrh.pantheonsite.io/"
		Then I should see "maclesp"
		Then I should see "here"

