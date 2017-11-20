## feature-php

### Example

```php
<?

/*
 * This is a simple example for the usage of the feature-php library.
 * Here we are going to analyze a given feature model regarding a given configuration.
 * 
 * Feature models and configurations are expected to be supplied as
 * FeatureIDE XML files, see https://featureide.github.io/.
 */

namespace FeaturePhp; // this is just for convenience so we can omit the prefix "FeaturePhp\" below

require "vendor/autoload.php"; // include classes from Composer

// feature-php may throw exceptions, in particular on parsing errors.
// We just output an exception if we get one.
try {

    // read a product line settings file, containing the model and the default configuration
    $productLine = new ProductLine\ProductLine(ProductLine\Settings::fromFile("config.json"));

    // alternatively you can supply the settings directly as an array
    $productLine = new ProductLine\ProductLine(ProductLine\Settings::fromArray(array(
        "model" => "model.xml",
        "defaultConfiguration" => array(
            "data" => "<configuration></configuration>"
        )
    )));

    // the configuration is user-supplied with the GET or POST parameter "configuration"
    // we could also use XmlConfiguration::fromString(...) or XmlConfiguration::fromFile(...)
    if (isset($_REQUEST["configuration"]))
        $configuration = new Model\Configuration(
            $productLine->getModel(),
            Model\XmlConfiguration::fromRequest("configuration")
        );
    else
        $configuration = null; // if not supplied, use the default configuration

    // just output some information on the model and configuration
    $productLine->renderAnalysis($configuration);
    
} catch (\Exception $e) {
    echo $e->getMessage();
}

?>
```