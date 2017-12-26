## feature-php

feature-php is a [Composer
package](https://packagist.org/packages/ekuiter/feature-php) for analyzing and
implementing feature-oriented software product lines.

It can be used to:

- analyze [FeatureIDE](https://featureide.github.io/) feature models and
  configurations (*domain analysis*)
- implement features using the following variability mechanisms (*domain
  implementation*):
  - runtime variability for PHP code
  - build-system-like copying of files and directories
  - preprocessor-like template and chunk systems
  - feature-oriented programming (mixin-based)
  - aspect-oriented programming (using [Go!
    AOP](https://github.com/goaop/framework))
- generate products and export them (e.g. as a ZIP file) (*product derivation*)

(If you'd like some visual tools for feature models and configurations, have a
look at
[ekuiter/feature-configurator](https://github.com/ekuiter/feature-configurator)
or [ekuiter/feature-model-viz](https://github.com/ekuiter/feature-model-viz).)

### Requirements

To use feature-php, PHP 5.3 is required with the SimpleXML extension. The DOM
extension and
[ekuiter/feature-schema](https://github.com/ekuiter/feature-schema) is used for
validating XML data and the zip extension is needed for exporting products as
ZIP files. [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser) is used for
feature- and aspect-oriented programming.

### Usage

Create a `composer.json` file in your project directory if you don't have one
yet. Add the package to the `require` section:

```
{
    "require": {
        "ekuiter/feature-php": "dev-master"
    }
}
```

Then run `composer install` in your project directory.

For a quick start, look at the example below (or try
[ekuiter/feature-web](https://github.com/ekuiter/feature-web)).

You can also run the command-line interface with `vendor/bin/feature-php`.

### API Reference

The API reference for feature-php can be found
[here](http://ekuiter.github.io/feature-php). A good starting point is the
[ProductLine](http://ekuiter.github.io/feature-php/classes/FeaturePhp.ProductLine.ProductLine.html)
class. If you want to learn about configuration files, have a look at the
[ProductLine\Settings](http://ekuiter.github.io/feature-php/classes/FeaturePhp.ProductLine.Settings.html)
class.

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
 * the feature-php API reference.
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
    // used for replacements by the templating system
    fphp\Specification\ReplacementRule::setConfiguration($configuration);

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

This project is released under the [LGPL v3 license](LICENSE.txt).