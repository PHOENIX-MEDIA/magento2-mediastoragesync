## Phoenix Media Storage Sync
The module retrieves files in /media from an origin server.

### What it does

Imagine you have a fresh local development environment with the Magento code checked out.
You retrieved the database but you don't have any of the media assets and your store frontend
just looks incomplete. You could grab a huge archive of the media folder from the production
environment but no one really wants to download tens of gigabytes just to work on a few catalog
pages.

This modules implements some plugins and observers that downloads images of categories,
products and CMS blocks/pages from a configurable origin server similar to a CDN the first time
you load an entity from the database. This means you can forget about the media folder and
just browse the frontend as images are downloaded and saved transparently.

### How it works

In the module's configuration you can configure a base URL, the domain where your production/staging
Magento instance is located from which to picked the database. In the database the relative
paths of categories and product images are stored. Once those entities are loaded the module
simply checks if their images are already in media/catalog. If not it uses the base URL,
appends the relative image path from the database and downloads the files from origin server.
This slows down page generation the first time you access a page but improves pretty quickly.

For other assets in the media folder we make use of another mechanism: Maybe you recognized
Magento is shipped with a get.php file in the Magento root. It was intended to retrieve image
data from a database, save it on the host's filesystem and then deliver it. Well, our assets
are located at a different web server but beside the retrieval of the asset data the rest is
pretty similar.
The get.php is called via a mod_rewrite rule in the media/.htaccess or equivalent rules in
your nginx configuration. The process is triggered every time a file in /media is not found
so it is only triggered the first time. The MediaStorageSync module downloads the file, saves
it and the get.php delivers the file. On the second load the web service can directly deliver
the static asset.

### How to use
**Install via Composer**
```
composer require phoenix-media/magento2-mediastoragesync
```

**Install via Modman**
```
modman clone git@github.com:PHOENIX-MEDIA/magento2-mediastoragesync.git
```


**Enable it**
``` 
bin/magento module:enable Phoenix_MediaStorageSync 
```

**Run setup upgrade and rebuild the DI cache**
``` 
bin/magento setup:upgrade 
```

### How to configure

- Find the modules configuration in the PHOENIX MEDIA section of your Magento configuration.
- Enable: Enable or disable the functionality
- URL: Configure the source URL where to retrieve the images (e.g. "https://magento.com/")

*Optionally configure credentials for BasicAuth*

### How to download wysiwyg images
Since wysiwyg images are dispatched via `./pub/get.php`, you can modify the code as shown below.

Find the following code in file `./pub/get.php`
```php
if (!$isAllowed($relativePath, $allowedResources)) {
    require_once 'errors/404.php';
    exit;
}
```
And replace by the chunk below
```php
if (!$isAllowed($relativePath, $allowedResources)) {              
    /* Customization by MagePsycho - Start */
    try {
        $absoluteFilePath = BP . '/pub/' . $relativePath;
        $absoluteFileUrl = '{your-live-url}/' . $relativePath;
        $absoluteFileDir = dirname($absoluteFilePath);
        if (!is_dir($absoluteFileDir)) {
            mkdir($absoluteFileDir, 777, true);
        }
        file_put_contents($absoluteFilePath, file_get_contents(trim($absoluteFileUrl)));
        file_put_contents(BP . '/log/download-image.log', $absoluteFilePath . ' -> ' . $absoluteFileUrl, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        require_once 'errors/404.php';
        exit;
    }
    /* Customization by MagePsycho - End */
}
```
Now your homepage will also look cool!