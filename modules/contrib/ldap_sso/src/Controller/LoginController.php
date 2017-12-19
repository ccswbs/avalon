<?php

namespace Drupal\ldap_sso\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ldap_authentication\Controller\LoginValidator;
use Drupal\ldap_sso\LdapSsoInterface;
use Drupal\ldap_sso\RedirectResponseWithCookie;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class LoginController.
 *
 * @package Drupal\ldap_sso\Controller
 */
class LoginController extends ControllerBase implements LdapSsoInterface {

  private $detailedWatchdogLog;
  private $config;
  private $logger;

  /**
   * Constructor containing logger and watchdog level.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logging interface.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Factory for configuration for LDAP and logging level.
   */
  public function __construct(LoggerInterface $logger, ConfigFactory $configFactory) {
    $this->logger = $logger;
    $this->detailedWatchdogLog = $configFactory->get('ldap_help.settings')->get('watchdog_detail');
    $this->config = $configFactory->get('ldap_sso.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.channel.ldap_sso'),
      $container->get('config.factory')
    );
  }

  /**
   * Login.
   *
   * A proxy function for the actual authentication routine. This is in place
   * so various implementations of grabbing NTLM credentials can be used and
   * selected from an administration page. This is the real gatekeeper since
   * this assumes that any NTLM authentication from the underlying web server
   * is good enough, and only checks that there are values in place for the
   * user name, and anything else that is set for a particular implementation.
   * In the case that there are no credentials set by the underlying web server,
   * the user is redirected to the normal user login form.
   */
  public function login() {
    if ($this->detailedWatchdogLog) {
      $this->logger->debug(
        'ldap_sso_user_login_sso.step1: implementation: @implementation, ' .
        'enabled: @enabled, server_remote_user: @server_remote_user, ' .
        'server_redirect_remote_user: @server_redirect_remote_user, ' .
        'ssoRemoteUserStripDomainName: @ssoRemoteUserStripDomainName,' .
        'seamlessLogin: @seamlessLogin',
        [
          '@implementation' => $this->config->get('ldapImplementation'),
          '@enabled' => $this->config->get('ssoEnabled'),
          '@server_remote_user' => isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : '',
          '@server_redirect_remote_user' => isset($_SERVER['REDIRECT_REMOTE_USER']) ? $_SERVER['REDIRECT_REMOTE_USER'] : '',
          '@ssoRemoteUserStripDomainName' => $this->config->get('ssoRemoteUserStripDomainName'),
          '@seamlessLogin' => $this->config->get('seamlessLogin'),
        ]
      );
    }

    $remote_user = NULL;
    $realm = NULL;
    $domain = NULL;

    switch ($this->config->get('ldapImplementation')) {
      case 'mod_auth_sspi':
        $remote_user = FALSE;
        if (isset($_SERVER['REMOTE_USER'])) {
          $remote_user = $_SERVER['REMOTE_USER'];
        }
        elseif (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
          $remote_user = $_SERVER['REDIRECT_REMOTE_USER'];
        }
        break;

      case 'mod_auth_kerb':
        $remote_user = FALSE;
        if (isset($_SERVER['REMOTE_USER'])) {
          $remote_user = $_SERVER['REMOTE_USER'];
        }
        elseif (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
          $remote_user = $_SERVER['REDIRECT_REMOTE_USER'];
        }

        if ($remote_user && preg_match('/^([A-Za-z0-9_\-\.]+)@([A-Za-z0-9_\-.]+)$/',
            $remote_user,
            $matches)) {
          $remote_user = $matches[1];
          // This can be used later if realms is ever supported properly.
          $realm = $matches[2];
        }
        break;
    }

    if ($this->detailedWatchdogLog) {
      $this->logger->debug(
        'ldap_sso_user_login_sso.implementation: username=@remote_user, (realm=@realm) found',
        [
          '@remote_user' => $remote_user,
          '@realm' => $realm,
        ]
      );
    }

    if ($remote_user) {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('User found, logging in.');
      }
      $this->loginRemoteUser($remote_user, $realm);
    }
    else {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('User missing.');
      }
      $this->remoteUserMissing();
      return new RedirectResponse(Url::fromRoute('user.login')->toString());
    }

    if ($this->config->get('cookieExpire') == -1) {
      // Length of session.
      $cookie_lifetime = 0;
    }
    else {
      // A value quickly in the past.
      $cookie_lifetime = 1;
    }

    // Invalidates cookie.
    $cookies[] = new Cookie('seamless_login_attempted', '', $cookie_lifetime, base_path());

    if (\Drupal::destination()->get() != '/') {
      $finalDestination = Url::fromUserInput(\Drupal::destination()->get())->setAbsolute()->toString();
    }
    else {
      $finalDestination = Url::fromRoute('<front>')->toString();
    }
    return new RedirectResponseWithCookie($finalDestination, 302, $cookies);
  }

  /**
   * Access callback.
   */
  public function access() {
    if (\Drupal::currentUser()->isAnonymous()) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

  /**
   * Perform the actual logging in of the user.
   *
   * @param string $remote_user
   *   Remote user name.
   * @param string $realm
   *   Realm information.
   */
  private function loginRemoteUser($remote_user, $realm) {
    if ($this->config->get('ssoRemoteUserStripDomainName')) {
      $remote_user = $this->stripDomainName($remote_user);

    }

    if ($this->detailedWatchdogLog) {
      $this->logger->debug(
        'ldap_sso_user_login_sso.remote_user: username=@remote_user, (realm=@realm) found',
        [
          '@remote_user' => $remote_user,
          '@realm' => $realm,
        ]
      );
    }

    $user = $this->validateUser($remote_user);

    if ($user && !$user->isAnonymous()) {
      $this->loginUserSetFinalize($user);
    }
    else {
      $this->loginUserNotSetFinalize();
    }
  }

  /**
   * Validate an unvalidated user.
   *
   * @param string $remote_user
   *   Remote user name.
   *
   * @return \Drupal\user\Entity\User
   *   Returns the user if available.
   */
  private function validateUser($remote_user) {
    if ($this->detailedWatchdogLog) {
      $this->logger->debug('Starting validation for SSO user.');
    }

    $validator = new LoginValidator();
    $validator->processSsoLogin(Html::escape($remote_user));

    if ($this->detailedWatchdogLog) {
      $this->logger->debug('Remote user has local uid @uid',
        ['@uid' => is_object($validator->drupalUser) ? $validator->drupalUser->id() : NULL]
      );
    }
    return $validator->drupalUser;
  }

  /**
   * Returns the relevant lifetime from configuration.
   *
   * @return int
   *   Expiration in seconds or 0 for session.
   */
  private function getCookieLifeTime() {
    if ($this->config->get('cookieExpire') == -1) {
      // Length of session.
      $cookie_lifetime = 0;
    }
    else {
      // A value quickly in the past.
      $cookie_lifetime = time();
    }
    return $cookie_lifetime;
  }

  /**
   * Finalize login with user not set.
   */
  private function loginUserNotSetFinalize() {
    if ($this->detailedWatchdogLog) {
      $this->logger->debug(
        'ldap_sso_user_login_sso.remote_user.user_fail.seamlessLogin'
      );
    }

    setcookie('seamless_login', LdapSsoInterface::AUTO_LOGIN_NO, $this->getCookieLifeTime(), base_path(), '');

    drupal_set_message(
      t(
        'Sorry, your LDAP credentials were not found or the LDAP server is not available. You may log in with other credentials on the %user_login_form.',
        ['%user_login_form' => Link::fromTextAndUrl('login form', Url::fromRoute('user.login'))]
      ),
      'error'
    );
    if ($this->detailedWatchdogLog) {
      $this->logger->debug('User not found or server error, redirecting to front page');
    }
  }

  /**
   * Finalize login with user set.
   *
   * @param \Drupal\user\UserInterface $account
   *   Valid user account.
   */
  private function loginUserSetFinalize(UserInterface $account) {
    if ($this->detailedWatchdogLog) {
      $this->logger->debug('Success with seamless login');
    }

    setcookie('seamless_login', LdapSsoInterface::AUTO_LOGIN_YES, $this->getCookieLifeTime(), base_path(), '');

    user_login_finalize($account);

    drupal_set_message(t('You have been successfully authenticated'));

    if ($this->detailedWatchdogLog) {
      $this->logger->debug('Login successful, redirecting to front page.');
    }
  }

  /**
   * Handle missing remote user.
   */
  private function remoteUserMissing() {
    $this->logger->debug(
      '$_SERVER[\'REMOTE_USER\'] not found', []
    );

    if ($this->detailedWatchdogLog) {
      $this->logger->debug('Authentication failure, redirecting to login');
    }

    setcookie('seamless_login', LdapSsoInterface::AUTO_LOGIN_NO, $this->getCookieLifeTime(), base_path(), 0);

    drupal_set_message(t('You were not authenticated by the server. You may log in with your credentials below.'), 'error');
  }

  /**
   * Strip the domain name from the remote user.
   *
   * @param string $remote_user
   *   The remote user name.
   *
   * @return string
   *   Returns the user without domain.
   */
  private function stripDomainName($remote_user) {
    // Might be in the form of <remote_user>@<domain> or <domain>\<remote_user>.
    $domain = NULL;
    $exploded = preg_split('/[\@\\\\]/', $remote_user);
    if (count($exploded) == 2) {
      if (strpos($remote_user, '@') !== FALSE) {
        $remote_user = $exploded[0];
        $domain = $exploded[1];
      }
      else {
        $domain = $exploded[0];
        $remote_user = $exploded[1];
      }
      if ($this->detailedWatchdogLog) {
        $this->logger->debug(
          'Domain stripped: remote_user=@remote_user, domain=@domain',
          [
            '@remote_user' => $remote_user,
            '@domain' => $domain,
          ]
        );
      }
    }
    return $remote_user;
  }

}
