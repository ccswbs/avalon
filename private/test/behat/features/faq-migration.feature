Feature: FAQ migration
	In order to verify news migrations ran at all
	As a maintainer
	I need to see that FAQ content came over

	Scenario: Basic FAQ sample content
		Given I am on "faq/what-programs-do-you-offer-minors"
		Then print current URL
		And I should see "In what programs do you offer Minors?"
		And I should see "Minors offered to you depend on the undergraduate calendar year"

	Scenario: FAQ sample content with markup
		Given I am on "faq/what-programs-do-you-offer"
		Then print current URL
		And I should see "What programs do you offer?"
		And the response should contain "<a href=\"https://admission.uoguelph.ca/41414b69-36f6-45e1-b4de-e32984142aa5\">alphabetical listing of majors</a>"
		And the response should contain "<a href=\"https://admission.uoguelph.ca/degrees\">list of degree programs</a>"


