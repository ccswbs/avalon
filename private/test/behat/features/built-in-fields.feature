Feature: Built-in Fields
	In order to verify migrations ran at all
	As a maintainer
	I need to see that content came over with the correct field values

	Scenario: An anonymous user should not be able access unpublished content
    		When I go to "unpublished-page"
    		Then I should see "You are not authorized to access this page."