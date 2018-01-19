Feature: UG FAQ
	In order to add FAQs to my site
	As an author
	I need views to list FAQs

	Scenario: FAQ listing view
		Given I am on "faq"
		Then I should see "What programs do you offer?"
		Then I should see "In what programs do you offer Minors?"

