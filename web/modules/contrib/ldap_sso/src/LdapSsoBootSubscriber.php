<?php

namespace Drupal\ldap_sso;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Url;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Provides a MyModuleSubscriber.
 */
class LdapSsoBootSubscriber implements EventSubscriberInterface, LdapSsoInterface {

  private $detailedWatchdogLog;
  private $config;
  private $logger;
  private $frontpage;
  private $currentPath;

  /**
   * Fetches debugging level and logging interface.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logging interface.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Factory for configuration for LDAP and logging level.
   * @param \Drupal\Core\Path\CurrentPathStack $currentPath
   *   Adds the current path.
   */
  public function __construct(LoggerInterface $logger, ConfigFactory $configFactory, CurrentPathStack $currentPath) {
    $this->logger = $logger;
    $this->detailedWatchdogLog = $configFactory->get('ldap_help.settings')->get('watchdog_detail');
    $this->config = $configFactory->get('ldap_sso.settings');
    $this->frontpage = $configFactory->get('system.site')->get('frontpage');
    $this->currentPath = $currentPath;
  }

  /**
   * Determine if we should attempt SSO.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event to act upon.
   */
  public function checkSsoLoad(GetResponseEvent $event) {

    if ((PHP_SAPI === 'cli') || \Drupal::currentUser()->isAnonymous() == FALSE) {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('CLI or logged in user');
      }
      return;
    }
    elseif ($this->checkExcludePath()) {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('Excluded path');
      }
      return;
    }
    elseif (!(isset($_COOKIE['seamless_login'])) || $_COOKIE['seamless_login'] == $this::AUTO_LOGIN_YES) {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('SSO Boot assumed');
      }
      $this->assumeSsoBoot();
    }
    else {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('Anonymous and no SSO boot.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkSsoLoad', 20];
    return $events;
  }

  /**
   * Continue booting assuming we are doing SSO.
   */
  private function assumeSsoBoot() {
    $path_args = explode('/', $this->currentPath->getPath());

    if ($path_args[0] == 'user' && !(is_numeric($path_args[1]))) {
      return;
    }
    elseif ($path_args[0] == 'user' && $path_args[1] == 'logout') {
      return;
    }
    else {
      if ($this->detailedWatchdogLog) {
        $this->logger->debug('Path is valid');
      }

      if (isset($_COOKIE['seamless_login_attempted'])) {
        if ($this->detailedWatchdogLog) {
          $this->logger->debug('Cookie present');
        }
        $loginAttempted = $_COOKIE['seamless_login_attempted'];
      }
      else {
        $loginAttempted = 'false';
      }

      if ($this->config->get('seamlessLogin') == 1 &&
        $loginAttempted == 'false') {

        if ($this->detailedWatchdogLog) {
          $this->logger->debug('Transferring to login controller');
        }

        if ($this->config->get('cookieExpire') == -1) {
          // Length of session.
          $cookie_lifetime = 0;
        }
        else {
          // A value quickly in the past.
          $cookie_lifetime = time();
        }

        $cookies[] = new Cookie('seamless_login_attempted', 'true', $cookie_lifetime, base_path());

        // This is set to destination() since the request uri is usually
        // system/40x already.
        $original_path = \Drupal::destination()->get();
        $pathWithDestination = Url::fromRoute('ldap_sso.login_controller')->toString() . '?destination=' . $original_path;
        $response = new RedirectResponseWithCookie($pathWithDestination, 302, $cookies);
        $response->send();
        exit(0);
      }
      else {
        // We need to send a empty response to not conflict with the termination
        // of subsequent requests (e.g. for assets) to not confuse the client.
        // We would like to send an empty 200 but have to send an error, to
        // redirection problems.
        // @FIXME: Resolve useless log entries from the 403 response.
        return Response::create('', 403);
      }
    }
  }

  /**
   * Exclude default excluded paths.
   */
  private function defaultPathsToExclude() {
    return [
      '/admin/config/search/clean-urls/check',
      '/user/login/sso',
    ];
  }

  /**
   * Check to exclude paths from SSO.
   *
   * @param bool|string $path
   *   Path to check for exclusion.
   *
   * @return bool
   *   Path excluded or not.
   */
  private function checkExcludePath($path = FALSE) {

    $result = FALSE;
    if ($path) {
      // don't derive.
    }
    elseif ($_SERVER['PHP_SELF'] == '/index.php') {
      $path = \Drupal::service('path.current')->getPath();
    }
    else {
      // cron.php, etc.
      $path = ltrim($_SERVER['PHP_SELF'], '/');
    }

    if (in_array($path, $this->defaultPathsToExclude())) {
      return TRUE;
    }

    if (is_array($this->config->get('ssoExcludedHosts'))) {
      $host = $_SERVER['SERVER_NAME'];
      foreach ($this->config->get('ssoExcludedHosts') as $host_to_check) {
        if ($host_to_check == $host) {
          return TRUE;
        }
      }
    }

    if ($this->config->get('ssoExcludedPaths')) {
      $patterns = implode("\r\n", $this->config->get('ssoExcludedPaths'));
      if ($patterns) {
        if (function_exists('drupal_get_path_alias')) {
          $path = drupal_get_path_alias($path);
        }
        $path = Unicode::strtolower($path);

        // Replacements for newlines, asterisks, and the <front> placeholder.
        $to_replace = [
          '/(\r\n?|\n)/',
          '/\\\\\*/',
          '/(^|\|)\\\\<front\\\\>($|\|)/',
        ];
        $replacements = [
          '|',
          '.*',
          '\1' . preg_quote($this->frontpage, '/') . '\2',
        ];
        $patterns_quoted = preg_quote($patterns, '/');
        $regex = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
        $result = (bool) preg_match($regex, $path);
      }
    }

    return $result;
  }

}
