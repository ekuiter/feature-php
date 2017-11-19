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

    (new Demo( // just output some information on the model and configuration
        new Configuration(

            // load the model from a file on the server, we could also use XmlModel::fromString(...)
            new Model(XmlModel::fromFile("model.xml")),

            // the configuration is user-supplied with the GET or POST parameter "configuration"
            // we could also use XmlConfiguration::fromString(...) or XmlConfiguration::fromFile(...)
            // true means it's allowed to not supply a configuration at all (implying an empty configuration)
            XmlConfiguration::fromRequest("configuration", true)
        )
    ))->render();
    
} catch (\Exception $e) {
    echo $e->getMessage();
}

?>
```