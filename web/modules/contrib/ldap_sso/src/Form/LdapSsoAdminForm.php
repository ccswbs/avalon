<?php

namespace Drupal\ldap_sso\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Url;
use Drupal\ldap_authentication\Helper\LdapAuthenticationConfiguration;

/**
 * Provides the configuration form SSO under LDAP configuration.
 */
class LdapSsoAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ldap_sso_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ldap_sso.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ldap_sso.settings');

    $form['information'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<h2>Single sign-on (SSO)</h2><p>Single sign-on enables users of this site to be authenticated by visiting the URL /user/login/sso, or automatically if selected below. Set up of LDAP authentication must be performed on the web server. Please review the README file for more information.</p>', [
        '@link' => Url::fromRoute('system.modules_list')->toString(),
      ]),
    ];

    $form['enabled'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<h4>Single sign-on is enabled.</h4> To disable it, uninstall the ldap_sso module on the <a href="@link">module uninstall page</a>', [
        '@link' => Url::fromRoute('system.modules_uninstall')->toString(),
      ]),
    ];

    $form['ssoRemoteUserStripDomainName'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Strip REMOTE_USER domain name'),
      '#description' => $this->t('Useful when the WWW server provides authentication in the form of user@realm and you want to have both SSO and regular forms based authentication available. Otherwise duplicate accounts with conflicting e-mail addresses may be created.'),
      '#default_value' => $config->get('ssoRemoteUserStripDomainName'),
    ];

    $form['seamlessLogin'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Turn on automated single sign-on'),
      '#description' => $this->t('This requires that you have operational NTLM or Kerberos authentication turned on for at least the path user/login/sso, or for the whole domain.'),
      '#default_value' => $config->get('seamlessLogin'),
    ];

    $form['cookieExpire'] = [
      '#type' => 'select',
      '#title' => $this->t('Cookie Lifetime'),
      '#description' => $this->t('If using the seamless login, a cookie is necessary to prevent automatic login after a user manually logs out. Select the lifetime of the cookie.'),
      '#default_value' => $config->get('cookieExpire'),
      '#options' => [
        -1 => t('Session'),
        0 => t('Immediately'),
      ],
    ];

    $form['ldapImplementation'] = [
      '#type' => 'select',
      '#title' => $this->t('Authentication Mechanism'),
      '#description' => $this->t('Select the type of authentication mechanism you are using.'),
      '#default_value' => $config->get('ldapImplementation'),
      '#options' => [
        'mod_auth_sspi' => t('mod_auth_sspi'),
        'mod_auth_kerb' => t('mod_auth_kerb'),
      ],
    ];

    $form['ssoExcludedPaths'] = [
      '#type' => 'textarea',
      '#title' => $this->t('SSO Excluded Paths'),
      '#description' => $this->t("Which paths will not check for SSO? cron.php is common example. Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard.
        Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.",
        ['%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>']),
      '#default_value' => LdapAuthenticationConfiguration::arrayToLines($config->get('ssoExcludedPaths')),
    ];

    $form['ssoExcludedHosts'] = [
      '#type' => 'textarea',
      '#title' => $this->t('SSO Excluded Hosts'),
      '#description' => $this->t('If your site is accessible via multiple hostnames, you may only want
        the LDAP SSO module to authenticate against some of them. To exclude
        any hostnames from SSO, enter them here. Enter one host per line.'),
      '#default_value' => LdapAuthenticationConfiguration::arrayToLines($config->get('ssoExcludedHosts')),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $factory = \Drupal::service('ldap.servers');
    $enabled_servers = $factory->getEnabledServers();

    /* @var \Drupal\ldap_servers\Entity\Server $server */
    foreach ($enabled_servers as $server) {
      if ($server->get('bind_method') == 'user' || $server->get('bind_method') == 'anon_user') {
        $methods = [
          'user' => $this->t('Bind with Users Credentials'),
          'anon_user' => $this->t('Anonymous Bind for search, then Bind with Users Credentials'),
        ];

        $form_state->setErrorByName('ssoEnabled', $this->t("Single sign-on is not valid with the server %sid because that server configuration uses %bind_method. Since the user's credentials are never available to this module with single sign-on enabled, there is no way for the ldap module to bind to the ldap server with credentials.",
          [
            '%sid' => $server->id(),
            '%bind_method' => $server->getFormattedBind(),
          ]
        ));
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ldap_sso.settings')
      ->set('ssoExcludedPaths', LdapAuthenticationConfiguration::linesToArray($values['ssoExcludedPaths']))
      ->set('ssoExcludedHosts', LdapAuthenticationConfiguration::linesToArray($values['ssoExcludedHosts']))
      ->set('ssoRemoteUserStripDomainName', $values['ssoRemoteUserStripDomainName'])
      ->set('seamlessLogin', $values['seamlessLogin'])
      ->set('cookieExpire', $values['cookieExpire'])
      ->set('ldapImplementation', $values['ldapImplementation'])
      ->save();
  }

}
