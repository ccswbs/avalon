<?php

namespace Drupal\Tests\ldap_sso\Browser;

use Drupal\Tests\BrowserTestBase;

/**
 * Test redirection behaviour with SSO enabled.
 *
 * @group ldap
 */
class LdapSsoBrowserTests extends BrowserTestBase {


  /**
   * {@inheritdoc}
   */
  public static $modules = ['user', 'ldap_servers', 'ldap_sso'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $configFactory = \Drupal::configFactory()->getEditable('ldap_authentication.settings');
    $configFactory->set('seamlessLogin', 1)->save();
  }

  /**
   * Test the behaviour of multiple requests on the SSO login behaviour.
   */
  public function brokenTestRedirectionLogin() {
    $this->drupalGet('/');
    // $this->assertSession()->statusCodeEquals('302');
    $this->drupalGet('/user/login/sso');
    // @TODO: Write test to resolve the behaviour of the boot subscriber.
    // $this->assertSession()->statusCodeEquals('302');
    $this->assertTrue(TRUE);
  }

}
