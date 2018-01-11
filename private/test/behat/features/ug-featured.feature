Feature: UG Featured Items
	In order to add featured items to my site
	As an author
	I need views to list featured items

	Scenario: Featured items teaser block
		Given I am on "/"
		Then I should see "Test Features"
		Then I should see "Laoreet Typicus"
		Then I should see "Summary"
		Then I should see "Defui hos nisl saluto velit venio"

	Scenario: Featured items listing view
		Given I am on "features"
		Then I should see "Laoreet Typicus"
		Then I should see "Illum Turpis"
		Then I should see "Camur Iustum Quibus Sed"

	Scenario: Featured items listing view (filtered)
		Given I am on "features/33"
		Then I should see "Laoreet Typicus"
		Then I should not see "Illum Turpis"
		Then I should not see "Camur Iustum Quibus Sed"

		Given I am on "features/24"
		Then I should not see "Laoreet Typicus"
		Then I should see "Illum Turpis"
		Then I should see "Lobortis"
