# EdgarEzUIBookmarkBundle

## Installation

### Get the bundle using composer

Add EdgarEzUIBookmarkBundle by running this command from the terminal at the root of
your symfony project:

```bash
composer require edgar/ez-uibookmark-bundle
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
        new Edgar\EzUIBookmarkBundle\EdgarEzUIBookmarkBundle(),
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
        
edgar.ezuibookmark:
    resource: '@EdgarEzUIBookmarkBundle/Resources/config/routing.yml'
    defaults:
        siteaccess_group_whitelist: 'admin_group'    
```

## Manage assets

```
php bin/console assets:install --symlink web
php bin/console assetic:dump
```
