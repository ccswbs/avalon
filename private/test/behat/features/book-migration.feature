Feature: Book migration
	In order to verify migrations ran at all
	As a maintainer
	I need to see that a node has moved from Drupal

	Scenario: Book page content
		Given I am on "book-page/quia-singularis-typicus"
		Then print current URL
		Then I should see "Quia Singularis Typicus"

