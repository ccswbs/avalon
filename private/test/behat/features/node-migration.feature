Feature: WYSIWYG settings
	In order to verify migrations ran at all
	As a maintainer
	I need to see that wysiwyg settings are maintained 

	Scenario: Long Text (Full HTML)
		Given I am on "media-listing-page"
		Then print current URL
		Then the response should contain "<div class=\"media-listing-page\">"
		Then I should see "Hello JavaScript!"

	Scenario: Long Text (Filtered HTML)
		Given I am on "filtered-listing-page"
		Then print current URL
		Then the response should not contain "<div class=\"media-listing-page\">"
		Then I should not see "Hello JavaScript!"