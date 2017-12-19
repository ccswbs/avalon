
# LDAP single sign-on

This module was originally part of the LDAP module and has been split off into
its own module during the migration to Drupal 8.

## Requirements

To use the single sign-on feature, your web server must provide an
authentication mechanism for LDAP. In theory, so long as the web server's LDAP
authentication mechanism is configured to provide the `$_SERVER` variable
`$_SERVER['REMOTE_USER']` or `$_SERVER['REDIRECT_REMOTE_USER']` corresponding
directly to a user's LDAP user name, your configuration should be supportable.

Only the following modules are directly supported as standard cases:
* [mod_auth_sspi](http://mod-auth-sspi.sourceforge.net/) recommended for Windows
* [mod_auth_kerb](http://modauthkerb.sourceforge.net/) recommended for Linux

If a Linux distribution is being used, the Apache authentication modules are
likely available within the distro's package manager.

Unless an administrator wishes to require that all visitors be authenticated,
NTLM and/or basic authentication should be set up only on the path
user/login/sso, which will authentify the visitor but not deny access to view
the site if the visitor is not authenticated. An administrator may wish to
require LDAP authentication to view any portion of the site; this can be
achieved by changing the location directive below to "/". An administrator may
also wish to automatically log in visitors to Drupal; this can be achieved by
checking "Turn on automated single sign-on" in the modules' configuration page.

# Apache example configuration

An example of an Apache configuration for a named virtualhost configuration
using mod_auth_sspi on Windows is as follows:

## httpd.conf:

### Virtual hosts
`Include conf/extra/httpd-vhosts.conf`

### Pass NTLM authentication to Apache
```
LoadModule sspi_auth_module modules/mod_auth_sspi.so

<IfModule !mod_auth_sspi.c>
  LoadModule sspi_auth_module modules/mod_auth_sspi.so
</IfModule>
```

## httpd-vhosts.conf:

```
NameVirtualHost example.com

<VirtualHost example.com>
  DocumentRoot "D:/www/example.com/htdocs"
  ServerName example.com

  <directory "D:/www/example.com/htdocs">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order Allow,Deny
    Allow from all
  </directory>

  <Location /user/login/sso>
    AuthType SSPI
    AuthName "Example.com - Login using your LDAP user name and password"
    SSPIAuth On
    SSPIAuthoritative On
    ### The domain used to authenticate with LDAP; this should match the domain
    ### configured in the LDAP integration configuration within Drupal
    SSPIDomain ad.example.com
    SSPIOmitDomain On
    SSPIOfferBasic On
    Require valid-user
    #SSPIBasicPreferred On
    #SSPIofferSSPI off
  </Location>
</VirtualHost>
```

# Additional information

After enabling and configuring an LDAP authentication module within Apache,
visit user/login/sso in the Drupal installation on example.com. With or without
the ldap sso feature enabled, the browser should prompt for a user name and
password if using Internet Explorer 8 or a non-Microsoft browser. Internet
Explorer 7 by default will pass NTLM authentication credentials to local
websites, and IE8 and Firefox can be configured to do this as well.

If prompted for credentials on that path, enter a valid LDAP user name, omitting
the domain if "SSPIOmitDomain On" is configured, as well as a password. If the
credentials are correct, or if NTLM credentials are passed automatically by the
browser and successfully authenticated, a Drupal 404 "Page not found" message
will be displayed if the module is not enabled; an "access is denied" message
will be displayed if the module is enabled and the browser is already logged in;
and if the ldap_sso module is fully configured and there is no existing session,
the browser will display the message "You have been successfully authenticated"
after redirecting to the sites' home page.
