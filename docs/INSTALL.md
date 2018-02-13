# EdgarEzTFABundle

## Installation

### Get the bundle using composer

Add EdgarEzTFABundle by running this command from the terminal at the root of
your symfony project:

```bash
composer require edgar/ez-tfa-bundle
```

## Enable the bundle

To start using the bundle, register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Edgar\EzUIProfileBundle\EdgarEzUIProfileBundle(),
        new Edgar\EzTFABundle\EdgarEzTFABundle(),
        // ...
    );
}
```

## Add doctrine ORM support

in your ezplatform.yml, add

```yaml
doctrine:
    orm:
        auto_mapping: true
```

## Update your SQL schema

```
php bin/console doctrine:schema:update --force
```

## Add routing

Add to your global configuration app/config/routing.yml

```yaml
edgar.ezuiprofile:
    resource: '@EdgarEzUIProfileBundle/Resources/config/routing.yml'
    defaults:
        siteaccess_group_whitelist: 'admin_group'
        
edgar.eztfa:
    resource: "@EdgarEzTFABundle/Resources/config/routing.yml"
    prefix:   /_tfa    
```

## Configure bundle

Two providers are natively available:
* email
* sms
* u2f (no configuration needed)

### SMS Provider initialization

Subscribe to OVH SMS Service to obtain api keys

https://www.ovhtelecom.fr/sms/#order-SMS

Go to API key page to generate application and consumer keys

https://api.ovh.com/createToken/

### Providers configuration

```yaml
# app/config/config.yml
edgar_ez_tfa:
    system:
        admin: # TFA is activated only for this siteaccess
            providers:
                email:
                    from: no-spam@your.mail # email provider sender mail
                sms:
                    application_key: <ovh_application_key>
                    application_secret: <ovh_application_secret>
                    consumer_key: <ovh_consumer_key>                    
```

Notes:
* don't activate TFA for all site, specially for back-office siteaccess
* you should use HTTPS for U2F authentication
* U2F work only with Chrome browser
 
If you want to disable a TFA provider, just add *disabled: true* parameter, example :

```yaml
# app/config/config.yml
edgar_ez_tfa:
    system:
        admin: # TFA is activated only for this siteaccess
            providers:
                email:
                    from: no-spam@your.mail # email provider sender mail
                sms:
                    application_key: <ovh_application_key>
                    application_secret: <ovh_application_secret>
                    consumer_key: <ovh_consumer_key>
                u2f:
                    disabled: true
```
