## feature-php

This is a work-in-progress [Composer package](https://packagist.org/packages/ekuiter/feature-php)
for analyzing FeatureIDE feature models and generating tailored variants of software product lines.

[Here](https://github.com/ekuiter/feature-example) you can find an example of its usage
as well as below.

(If you'd like to guide the user in creating a configuration in the browser, have a look at
[ekuiter/feature-configurator](https://github.com/ekuiter/feature-configurator).)


### Requirements

PHP 5.3 is required with the SimpleXML extension. The zip extension is needed for exporting products as ZIP files.

### Getting started

Create a `composer.json` file in your project directory if you don't have one yet.
Add the package to the `require` section:
```
{
    "require": {
        "ekuiter/feature-php": "dev-master"
    }
}
```
Then run `composer install` in your project directory. For usage, see the documentation or example below.

### Documentation

The documentation for feature-php can be found [here](http://ekuiter.github.io/feature-php). A good starting
point is the [ProductLine](http://ekuiter.github.io/feature-php/classes/FeaturePhp.ProductLine.ProductLine.html)
class. If you want to learn about configuration files, have a look at the
[ProductLine\Settings](http://ekuiter.github.io/feature-php/classes/FeaturePhp.ProductLine.Settings.html) class.

### Example

```php
<?

/*
 * This is a simple example for the usage of the feature-php library.
 * Here we are going to analyze a given feature model regarding a given configuration.
 * Then, for a valid configuration, we analyze the generated product.
 * Finally, the user may export a ZIP file and download it.
 * 
 * Feature models and configurations are expected to be supplied as
 * FeatureIDE XML files, see https://featureide.github.io/.
 * The product line settings can be supplied in various formats, see
 * the feature-php documentation.
 */

use \FeaturePhp as fphp; // this is just for convenience so we can abbreviate the prefix "FeaturePhp\" below

require "vendor/autoload.php"; // include classes from Composer

// feature-php may throw exceptions, in particular on parsing errors.
// We just output an exception if we get one.
try {

    // read a product line settings file, containing the model and the default configuration
    $productLine = new fphp\ProductLine\ProductLine(fphp\ProductLine\Settings::fromFile("config.json"));

    // alternatively you can supply the settings directly as an array
    $productLine = new fphp\ProductLine\ProductLine(fphp\ProductLine\Settings::fromArray(array(
        "model" => "model.xml",
        "defaultConfiguration" => array(
            "data" => "<configuration></configuration>"
        )
    )));

    // the configuration is user-supplied with the GET or POST parameter "configuration"
    // we could also use XmlConfiguration::fromString(...) or XmlConfiguration::fromFile(...)
    if (isset($_REQUEST["configuration"]))
        $configuration = new fphp\Model\Configuration(
            $productLine->getModel(),
            fphp\Model\XmlConfiguration::fromRequest("configuration")
        );
    else // if not supplied, use the default configuration
        $configuration = $productLine->getDefaultConfiguration();

    if (!isset($_REQUEST["generate"])) {
        // output some information on the model and configuration
        echo '<h2><a href="?generate">Generate</a></h2>';
        echo $configuration->renderAnalysis();
        
    } else {
        // we want to generate or export a product
        $product = $productLine->getProduct($configuration);
        
        if (!isset($_REQUEST["export"])) {
            // output some information on the product
            echo '<h2><a href="?generate&export">Download ZIP</a></h2>';
            echo $product->renderAnalysis();
            
        } else
            // export product as ZIP file (using "tmp" as a temporary directory)
            $product->export(new fphp\Exporter\DownloadZipExporter("tmp"));
    }
    
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
```

### License

The rules from [uvr2web](https://github.com/ekuiter/uvr2web/blob/master/LICENSE.txt) apply.