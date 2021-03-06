<?php

namespace Drupal\ldap_sso;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * An HTTP response doing a redirect and sending cookies.
 *
 * @package AlterPHP.Component
 *
 * @subpackage HttpFoundation
 *
 * @author pcb <pc.bertineau@alterphp.com>
 */
class RedirectResponseWithCookie extends RedirectResponse {

  /**
   * Creates a redirect response so that it conforms to the rules defined for a
   * redirect status code.
   *
   * @param string $url
   *   The URL to redirect to.
   * @param int $status
   *   The status code (302 by default)
   * @param array $cookies
   *   An array of Cookie objects.
   */
  public function __construct($url, $status = 302, array $cookies = []) {

    parent::__construct($url, $status);
    foreach ($cookies as $cookie) {
      if (!$cookie instanceof Cookie) {
        throw new \InvalidArgumentException(sprintf('One of third parameter array is not a valid Cookie object.'));
      }
      $this->headers->setCookie($cookie);
    }
  }

}
