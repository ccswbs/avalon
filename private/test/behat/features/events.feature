Feature: events
	In order to view event content on my site
	As an anonymous user
	I need to be able to see an event node and event content on a listing page, teaser block, and in an RSS feed with correct formating.

   	# Event Lists
   	# -------------------

	Scenario: Upcoming events listing view
		Given I am on "events"
		Then print current URL
		Then I should see "Ibidem Illum Iustum"

	Scenario: Upcoming events RSS feed
		Given I am on "events/feed"
		Then the response should contain "Ibidem Illum Iustum"

	Scenario: Archived events page
		Given I am on "events/archive"
		Then the response should contain "Another All Day Event you should really attend"

	#
	# Filtered Event Lists
	# --------------------

	# Filter tests still need to be written - contextual filters are not yet part of the events listing view (E1)

	Scenario: Upcoming events listing view (filtered)
		Given I am on "events/term/42"
		Then I should see "Test Event No.1"
		Then I should not see "Ibidem Illum Iustum"

	Scenario: Upcoming events feed (filtered)
		Given I am on "events/term/42/feed"
		Then the response should contain "Test Event No.1"
		Then the response should not contain "Ibidem Illum Iustum"

	#
	# Event teaser block view
	# -----------------------

	# Not written - need to sort out block placement first.
