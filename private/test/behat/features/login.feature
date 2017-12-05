Feature: Drupal login from Behat
  In order to demonstrate the Drush driver works
  As a developer
  I need to successfully view a page as an authenticated user

  @api
  Scenario: Scenario uses drush driver to log in
    Given I am logged in as a user with the "authenticated user" role
    When I am on "user"
    Then I should not see "Log in"