Feature: Content migration
	In order to verify migrations ran at all
	As a maintainer
	I need to see that a node has moved from Drupal

	Scenario: An anonymous user should not be able access unpublished content
            When I go to "unpublished-page"
    		Then I should see "You are not authorized to access this page."
	
	Scenario: Book page
		Given I am on "book-page/quia-singularis-typicus"
		Then print current URL
		Then I should see "Quia Singularis Typicus"
		Then I should see "Abigo aliquam humo lobortis macto"

	Scenario Outline: Event page general layout
		Given I am on "<url>"
		Then print current URL
		Then I should see "<title>"
		Then I should see "<month> <day num>, <year>"
		Then I should see "<starttime>"
		# no <endtime> yet - need to account datetime range migration incompatibility
		Then I should see "<location>"
	
		Examples:
			| url                                     | title                                | day    | month    | day num | year | starttime | endtime | location | details                                                              |
    		| events/2017/12/event-has-picture-carrot | This event has a picture of a carrot | Monday | December | 18th    | 2017 | 3:15 PM   | 4:30 PM | room 89  |                                                                      |
    		| events/2017/11/30-minute-meating        | 30 minute meating                    | Monday | November | 27th    | 2017 | 3:00 PM   | 3:30 PM | 8        | sdafm msad mf sdaf l;sdafm sd fa sd fasd fafkl;sdfa'k yay 30mintues. |


	Scenario: Featured content item
		Given I am on "content/feature/illum-turpis"
		Then print current URL
		Then I should see "Illum Turpis"
		Then I should see "Image"
		Then the response should contain "<img src=\"/sites/default/files/field_feature_image/imagefield_jqDN4x.jpg\" width=\"541\" height=\"357\" alt=\"Ad melior nostrud. Abluo ad aptent laoreet plaga similis.\" title=\"Abico proprius vicis. Commodo consectetuer cui imputo ludus patria tincidunt.\" typeof=\"foaf:Image\" />"
		Then I should see "https://dev-hjckrrh.pantheonsite.io/"
		Then I should see "here"
		Then I should see "Capto os utrum. Eros saepius similis."
		Then I should see "huprauecrid"

	Scenario: Basic FAQ
		Given I am on "faq/what-programs-do-you-offer-minors"
		Then print current URL
		And I should see "In what programs do you offer Minors?"
		And I should see "Minors offered to you depend on the undergraduate calendar year"

	Scenario: FAQ with markup
		Given I am on "faq/what-programs-do-you-offer"
		Then print current URL
		And I should see "What programs do you offer?"
		And the response should contain "<a href=\"https://admission.uoguelph.ca/41414b69-36f6-45e1-b4de-e32984142aa5\">alphabetical listing of majors</a>"
		And the response should contain "<a href=\"https://admission.uoguelph.ca/degrees\">list of degree programs</a>"

	Scenario: News article
		Given I am on "news/2017/11/u-g-eco-friendly-flowerpots-blossom-us-canada"
		Then print current URL
		Then I should see "U of G Eco-Friendly Flowerpots Blossom in U.S., Canada"
		Then I should see "Written by University of Guelph"
		Then I should see "University of Guelph gryphon statue"
		Then the response should contain "<img src=\"/sites/default/files/UofG-Math-Stats-Stock-Web-FullSize-034%20copy.jpg\" width=\"1140\" height=\"291\" alt=\"University of Guelph gryphon statue\" typeof=\"foaf:Image\" />"
		Then I should see "Affordable, eco-friendly flowerpots "

	Scenario: Service page
		When I go to "services/test-service-type-thursday"
		Then I should see "test service type - thursday"
		Then I should see "some service related text will go here"
		Then I should see "100.10"