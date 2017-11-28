Feature: Featured Content migration
	In order to verify migrations ran at all
	As a maintainer
	I need to see that a featured content item has moved from Drupal

	Scenario: Illum Turpis featured content item
		Given I am on "content/feature/illum-turpis"
		Then print current URL
		Then I should see "Illum Turpis"
		Then I should see "Image"
		Then the response should contain "<img src=\"/sites/default/files/field_feature_image/imagefield_jqDN4x.jpg\" width=\"541\" height=\"357\" alt=\"Ad melior nostrud. Abluo ad aptent laoreet plaga similis.\" title=\"Abico proprius vicis. Commodo consectetuer cui imputo ludus patria tincidunt.\" typeof=\"foaf:Image\" />"
		Then I should see "https://dev-hjckrrh.pantheonsite.io/"
		Then I should see "here"
		Then I should see "Capto os utrum. Eros saepius similis."
		Then I should see "huprauecrid"

