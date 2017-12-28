## feature-php: User guide

Thank you for your interest in feature-php. This user guide should help you to
get up and running.

To see what feature-php is about, see the [README](README.md) file first. More
details on the topics covered here can be found in the [API
reference](http://ekuiter.github.io/feature-php).

If you have any questions or problems, please
[contact me](mailto:info@elias-kuiter.de).

### Contents

- [Installation](#installation)
- [Getting started](#getting-started)
- [Product line settings](#product-line-settings)
  - [Feature model](#feature-model)
  - [Default configuration](#default-configuration)
  - [Artifacts](#artifacts)
  - [Containment hierarchies](#containment-hierarchies)
- [Using feature-php](#using-feature-php)
  - [Domain analysis](#domain-analysis)
    - [Input validation](#input-validation)
  - [Domain implementation](#domain-implementation)
    - [Using replacement rules](#using-replacement-rules)
    - [Tracing links](#tracing-links)
  - [Product derivation](#product-derivation)
- [Artifact settings](#artifact-settings)
  - [Registering generators](#registering-generators)
  - [Generator settings](#generator-settings)
    - [Copy generator](#copy-generator)
    - [Runtime generator](#runtime-generator)
    - [Chunk generator](#chunk-generator)
    - [Template generator](#template-generator)
    - [Collaboration generator](#collaboration-generator)
    - [Aspect generator](#aspect-generator)

### Installation

You should start by creating a `composer.json` file in your project directory if
you don't have one yet. Add the feature-php package to the `require` section:

```json
{
    "minimum-stability": "dev",
    "require": {
        "ekuiter/feature-php": "dev-master"
    }
}
```

Then run `composer install` in your project directory. (If you don't have
Composer yet, it's a convenient [package manager](https://getcomposer.org/) for
PHP).

Then create a file beside the `vendor` directory, e.g. `index.php` and require
feature-php:

```php
<?php

use \FeaturePhp as fphp; // abbreviation for "FeaturePhp\"

require "vendor/autoload.php"; // include classes from Composer

// use feature-php ...

?>
```

(Of course you can also use feature-php with an existing Composer project. In
that case, make sure the main file (i.e. `index.php`) is beside the `vendor`
directory.)

Now you're all set for using feature-php!

### Getting started

The central object in feature-php is the *product line*, a container for

- the *feature model* (a tree-like structure that models the variability)
- the *default configuration* (the feature selection that is used by default)
- all *artifacts* (instructions and code that implement the features)

To create a product line object, you need to supply a *product line settings*
object that describes the model, configuration and artifacts.

The recommended way to store settings in feature-php is using JSON files which
are then loaded from your PHP code. The other way is to pass settings as plain
PHP arrays, which might be more flexible. I'll show both ways below.

Let's set up a simple product line in your `index.php` file (assuming the
namespace `fphp` as shown [above](#installation)):

```php
// way 1: load a JSON file in the root directory
$productLineSettings = fphp\ProductLine\Settings::fromFile("productLine.json");

// way 2: pass a plain PHP array
$productLineSettings = fphp\ProductLine\Settings::fromArray(
    array(
        "model" => array(
            "data" =>
            "<featureModel>
                <struct><feature name=\"root feature\"/></struct>
                <constraints/>
             </featureModel>"
        )
    )
);

// now you can create the product line object
$productLine = new fphp\ProductLine\ProductLine($productLineSettings);
```

If you want to load a JSON file, put this into `productLine.json`:

```json
{
    "model": {
        "data": "<featureModel><struct><feature name=\"root feature\"/></struct><constraints/></featureModel>"
    }
}
```

This product line consists only of a feature model (with only one feature).

Let's move on to a more detailed explanation of the product line settings.

### Product line settings

I'll only discuss the most important settings - for a complete reference, click
[here](http://ekuiter.github.io/feature-php/classes/FeaturePhp.ProductLine.Settings.html).

#### Feature model

Every product line requires a *feature model*. Feature models are provided in
[FeatureIDE](https://featureide.github.io/)'s XML format - you can read about
that [here](https://github.com/ekuiter/feature-schema) or just use FeatureIDE to
generate a feature model. As an example, I will use FeatureIDE's [own feature
model](https://raw.githubusercontent.com/FeatureIDE/FeatureIDE/develop/featuremodels/FeatureIDE/model.xml).

There are two ways to tell feature-php about the product line's feature model.
One is to write it directly as a string into `productLine.json` (as seen
[above](#getting-started)) using an object with a "data" key:

```json
{
    "model": {
        "data": "<featureModel>...</featureModel>"
    }
}
```

The recommended way though is to load the feature model from an XML file.

To do this, save your model into `model.xml` and update `productLine.json`:

```json
{
    "model": "model.xml"
}
```

#### Default configuration

A configuration is a selection of features that can be used to generate a
product (or variant) of the product line. Especially in development, it's useful
to have a *default configuration* which can be used when the user hasn't
supplied an own configuration.

Like feature models, configurations are provided in
[FeatureIDE](https://featureide.github.io/)'s XML format (see
[here](https://github.com/ekuiter/feature-schema)). You can generate
configurations with FeatureIDE or
[ekuiter/feature-configurator](https://ekuiter.github.io/feature-configurator/).

Also similar to feature models, you may provide the default configuration
directly in the `productLine.json` file ...

```json
{
    "defaultConfiguration": {
        "data": "<configuration>...</configuration>"
    }
}
```

... or load it from an XML file:

```json
{
    "defaultConfiguration": "configuration.xml"
}

```

#### Artifacts

Every feature in the feature model may correspond to one *artifact* in the
product line settings. An artifact contains all information needed by
feature-php to generate a product that contains the artifact's feature. As with
models and configurations, there are different ways to supply the artifact
settings.

The simplest method is to pass an object mapping features to artifacts in
`productLine.json`:

```json
{
    "artifacts": {
        "FeatureIDE": { ... },
        "AHEAD": { ... }
    }
}
```

(The individual artifact settings (`{ ... }`) will be discussed in detail
[below](#artifact-settings).)

To keep `productLine.json` simple, it is recommended to load the artifact
settings from other JSON files. To do this, specify an `artifactDirectory` in
`productLine.json`:

```json
{
    "artifactDirectory": "artifacts"
}
```

Now feature-php will look for files in the `artifacts` directory that are named
after features. To achieve the same as above, save the artifact settings for
FeatureIDE and AHEAD (`{ ... }` above) into `artifacts/FeatureIDE.json` and
`artifacts/AHEAD.json` respectively. (You can use "permissive" feature names
here, meaning `featureide.json` and `ahead.json` would also be valid).

##### Containment hierarchies

In most cases, your artifact will not only consist of the JSON file, but also
code, documentation and other files that should be grouped with the artifact.

To group an artifact and its files, create a new directory with the permissive
feature name inside the `artifactDirectory` specified in `productLine.json` (see
[above](#artifacts)). Put the artifact settings into an `artifact.json` in this
directory and all other files for this artifact as well.

For example, you may take `artifacts/AHEAD.json` from [above](#artifacts) and
simply rename it to `artifacts/AHEAD/artifact.json` to separate it cleanly from
other artifacts.

You may customize the file name inside the containing directory (e.g.
`artifact.json`) by specifying an `artifactFile` in `productLine.json`:

```json
{
    "artifactFile": "config.json"
}
```

Now the settings file from above should be named `artifacts/AHEAD/config.json`.

It is recommended to use simple JSON files (`artifacts/AHEAD.json`) for
artifacts that do not have any associated files, and containment hierarchies
(`artifacts/AHEAD/artifact.json`) for everything else.

Before diving into the actual artifact settings, you should learn how to work
with product lines, feature models and configurations in the PHP code.

### Using feature-php

feature-php supports different parts of the product-line engineering process.

#### Domain analysis

feature-php offers some facilities to analyze feature models and configurations.

A common use case is that the user supplies his own configuration that should be
analyzed or generated. feature-php can handle this use case out of the box
(assuming you followed the steps [above](#installation) and have a
`$productLine` object available):

```php
// the configuration is user-supplied with the GET or POST parameter "configuration"
// we could also use XmlConfiguration::fromString(...) or XmlConfiguration::fromFile(...)
if (isset($_REQUEST["configuration"]))
    $configuration = new fphp\Model\Configuration(
        $productLine->getModel(),
        fphp\Model\XmlConfiguration::fromRequest("configuration")
    );
else // if not supplied, use the default configuration
    $configuration = $productLine->getDefaultConfiguration();
```

Now that you have a configuration object, you can analyze it:

```php
echo $configuration->renderAnalysis($productLine);
```

Or you can derive a product from the configuration and analyze its files and
tracing links:

```php
echo $productLine->getProduct($configuration)->renderAnalysis();
```

Note that you can also analyze configurations and products by running the
command-line interface in the project directory:

```sh
# analyze the default configuration
vendor/bin/feature-php --settings productLine.json

# analyze the specified configuration
vendor/bin/feature-php --settings productLine.json --configuration configuration.xml

# analyze the product derived from the specified configuration
vendor/bin/feature-php --settings productLine.json --configuration configuration.xml --generate
```

##### Input validation

You may have already noticed that invalid feature models are rejected
automatically. The same is true for invalid configurations like:

```xml
<configuration>
    <feature name="FeatureIDE" automatic="selected"/>
</configuration>
```

... which yields `The attribute 'manual' is required but missing`.

But the following (seemingly valid) configuration is also rejected:

```xml
<configuration>
    <feature name="FeatureIDE" automatic="selected" manual="unselected"/>
</configuration>
```

This is because feature-php also considers the semantics of configurations. For
a configuration to be valid, every feature must appear in the configuration and
be either selected or unselected. Further, all constraints given in the feature
model must be satisfied.

This implies that, using `XmlConfiguration::fromRequest`, no user will ever be
able to derive an invalid product, which makes this function production-safe.

#### Domain implementation

feature-php supports different approaches to variability implementation. The
approaches are discussed in detail [below](#artifact-settings) and are primarily
implemented using artifact settings.

There are some things to note here, though:

##### Using replacement rules

If you use the [template generator](#template-generator), make sure to include
this line somewhere before generating a product (where `$configuration` is the
configuration you'd like to derive from):

```php
fphp\Specification\ReplacementRule::setConfiguration($configuration);
```

This is necessary if the feature model contains value features (features that
carry a string value).

##### Tracing links

feature-php has a simple mechanism for detecting so-called *tracing links*.
These links provide information about which feature is responsible for each part
of the generated product. This is useful for debugging and larger product lines.

To get all the tracing links for a product, simply analyze it (see
[above](#domain-analysis)):

```php
echo $productLine->getProduct($configuration)->renderAnalysis();
```

You can also get all tracing links for a given feature with the name `$feature`:

```php
$artifact = $productLine->getArtifact($productLine->getFeature($feature));
echo $productLine->renderTracingLinkAnalysis($artifact);
```

You can also obtain tracing links with the command-line interface:

```sh
# get all tracing links for the feature with the name <feature>
vendor/bin/feature-php --settings productLine.json --trace <feature>
```

#### Product derivation

Finally, feature-php is able to generate a product (i.e. create files using the
artifacts) and export it (by downloading a ZIP file or storing the product
locally).

Deriving and analyzing a product from a configuration is straightforward (see
[above](#domain-analysis)):

```php
$product = $productLine->getProduct($configuration);
echo $product->renderAnalysis();
```

You can export a product with an *exporter* object:

```php
// Offers the product for download as a ZIP file, using "tmp" as a
// temporary directory for ZIP creation. Make sure "tmp" exists
// and is writable for the PHP server!
$exporter = new fphp\Exporter\DownloadZipExporter("tmp");

// Stores the generated product into "installDirectory" (which is created).
// If $overwrite is false, this fails if "installDirectory" already exists.
$exporter = new fphp\Exporter\LocalExporter("installDirectory", $overwrite);

$product->export($exporter);
```

Local export is also supported by the command-line interface:

```sh
# export the product derived from the specified configuration into the directory <installDirectory>
vendor/bin/feature-php --settings productLine.json --configuration configuration.xml --export <installDirectory>
```

### Artifact settings

You already know how to add artifacts to your product line. Now you'll get to
know the actual settings for these artifacts - this is the key to implementing
your product line's features.

The key concept for implementing artifacts is the *generator*. Every artifact
may register itself with some generators and pass some settings to it. The
generator is then responsible for deriving the actual files. There are different
generators, implementing different variability techniques.

#### Registering generators

You can register a generator similar to how you specified artifacts in
`productLine.json`, i.e. with an object mapping generators to generator settings.

Suppose you have some artifact settings at `artifacts/FeatureIDE.json` and you
want to register the *runtime* and *copy* generators - this is what it might
look like:

```json
{
    "runtime": true,
    "copy": { ... }
}
```

To make it more clear that these are generators, you may also write:

```json
{
    "generators": {
        "runtime": true,
        "copy": { ... }
    }
}
```

The individual generator settings (`{ ... }`) are discussed
[below](#generator-settings). (The *runtime generator* is special in the sense
that it may only take `true` as a setting.)

#### Generator settings

Like [above](#product-line-settings), I'll only discuss the most important
settings - for a complete reference, click
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Generator.Settings.html).

Note that, for some generators, you can not only pass settings to the registered
artifacts, but also inside the product line settings in `productLine.json`:

```json
{
    "generators": {
        "copy": { ... }
    }
}

```

##### Copy generator

The *copy generator* is used to copy files and directories into the product if a
feature is selected. In a sense it acts like a simple build system.

For example, you can configure an artifact located in
`artifacts/FeatureIDE/artifact.json` to copy the directory
`artifacts/FeatureIDE/src` recursively to the target path `FeatureIDE` like
this:

```json
{
    "copy": {
        "directories": [
            { "source": "src", "target": "FeatureIDE" }
        ]
    }
}
```

This example is slightly more complex, copying a single file `file.php` as
well and excluding the `.gitignore` file and the subdirectory `test`:

```json
{
    "copy": {
        "files": [
            { "source": "file.php", "target": "FeatureIDE/file.php" }
        ],
        "directories": [
            {
                "source": "src",
                "target": "FeatureIDE",
                "exclude": [".gitignore", "test/*"]
            }
        ]
    }
}
```

Often, all files for an artifact share the same target (e.g. `FeatureIDE`). Then
you can define a common target in the copy generator settings and simplify the
specification for `file.php`:

```json
{
    "copy": {
        "target": "FeatureIDE",
        "files": ["file.php"],
        "directories": [
            {
                "source": "src",
                "target": ".",
                "exclude": [".gitignore", "test/*"]
            }
        ]
    }
}
```

It is also possible to globally exclude files such as `.gitignore` in the
product line settings, see
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Generator.Settings.html).

##### Runtime generator

The *runtime generator* creates a PHP class which contains information on
selected and deselected features and which can be called at runtime. This
enables runtime variability.

It can be registered like this:

```json
{
    "runtime": true
}
```

A class is generated (`Runtime.php` by default) that you can include in your
code. To ask whether a registered feature is selected or deselected, write:

```php
if (Runtime::hasFeature("...")) {
   /* ... */
}
```

You can customize some aspects of the runtime class (like its name), see
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Generator.Settings.html).

##### Chunk generator

The *chunk generator* can assemble files from multiple chunks of text. An
artifact can specify a chunk of text like this:

```json
{
    "chunk": {
        "files": [
            { "target": "README.md", "text": "Some text to include if selected" }
        ]
    }
}
```

Different artifacts can extend the same chunked file, e.g. to create tailored
documentation. It's also possible to include a chunk when a feature is
deselected:

```json
{
    "chunk": {
        "filesIfSelected": [
            { "target": "README.md", "text": "Some text to include if selected" }
        ],"filesIfDeselected": [
            { "target": "README.md", "text": "Some text to include if deselected" }
        ]
    }
}
```

At least one artifact or the product line has to set the `mayCreate` option to
`true`, otherwise the chunked file will not be included:

```json
{
    "chunk": {
        "files": [
            { "target": "README.md", "mayCreate": true }
        ]
    }
}
```

Chunked files can additionally have a header and footer, see
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Specification.ChunkSpecification.html).

##### Template generator

The *template generator* takes existing files and replaces specific parts to
generate new, configuration-specific files. Artifacts can specify *replacement
rules* like this:

```json
{
    "template": {
        "files": [
            { "source": "file.php", "target": "file.php", "rules": [...] }
        ]
    }
}
```

How to specify replacement rules (`[...]`) is discussed below.

Different artifacts can specify replacements on the same file. It's also
possible to do a replacement when a feature is deselected:

```json
{
    "template": {
        "filesIfSelected": [
            { "source": "file.php", "target": "file.php", "rules": [...] }
        ],
        "filesIfDeselected": [
            { "source": "file.php", "target": "file.php", "rules": [...] }
        ]
    }
}
```

At least one artifact or the product line has to set the `mayCreate` option to
`true`, otherwise the template file will not be included:

```json
{
    "chunk": {
        "files": [
            { "source": "file.php", "target": "file.php", "mayCreate": true }
        ]
    }
}
```

The different types of replacement rules you can specify are explained
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Specification.ReplacementRule.html).

##### Collaboration generator

*Collaborations* are a concept from feature-oriented programming where so-called
*roles* are used to *refine* classes. Depending on the configuration, different
classes are composed. Class composition in feature-php is mixin-based.

Suppose you want to implement a graph library (feature *Base*) with optional
features *Weighted* and *Colored*. You created a base class and refinements for
these features:

```php
// base role in base/Edge.php (introduces Edge)
class Edge {
    // ...
}

// refining role in weighted/Edge.php (refines Edge)
class Edge {
    protected $weight;
    // ...
}

// refining role in colored/Edge.php (refines Edge)
class Edge {
    protected $color;
    // ...
}

// base role in colored/Color.php (introduces Color)
class Color {
    // ...
}
```

(Note that every refinement file must contain exactly one class definition.)

Now, to compose these refinements, specify them in the artifact (similar to the
[copy generator](#copy-generator)) - put this into `base/artifact.json` and
`weighted/artifact.json`:

```json
{
    "collaboration": {
        "files": "Edge.php"
    }
}
```

... and this into `colored/artifact.json` because *Colored* refines two classes:

```json
{
    "collaboration": {
        "files": ["Edge.php", "Color.php"]
    }
}
```

Finally you have to specify the order in which the refinements should be
composed. This is done in the product line settings in `productLine.json`:

```json
{
    "collaboration": {
        "featureOrder": ["Base", "Weighted", "Colored"]
    }
}
```

You can learn more about collaborations
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Generator.CollaborationGenerator.html).

##### Aspect generator

*Aspects* are used in aspect-oriented programming. The concept is similar to
feature-oriented programming, but more complex and powerful.

An aspect defines chunks of code (*advices*) which are executed on specific
events in the control flow (filtered by *pointcuts*). Aspects are especially
well-suited for cross-cutting concerns like logging or caching.

feature-php doesn't implement aspect-oriented programming, it rather uses the
[Go! AOP](https://github.com/goaop/framework) library. All aspects registered
with selected artifacts are compiled into an aspect kernel that you can
initialize at runtime.

Let's implement a simple debugging aspect that intercepts every database query
and logs it onto the screen. Put this into `debug/DebugAspect.php`:

```php
<?php

class DebugAspect implements Go\Aop\Aspect {
    /**
     * @Go\Lang\Annotation\Before("execution(public Database::*(*))")
     */
    public function beforeMethodExecution(Go\Aop\Intercept\MethodInvocation $invocation) {
        $obj = $invocation->getThis();
        $args = array();
        foreach ($invocation->getArguments() as $arg)
            $args[] = json_encode($arg);
            
        echo '<pre>', is_object($obj) ? get_class($obj) : $obj,
             $invocation->getMethod()->isStatic() ? '::' : '->',
             $invocation->getMethod()->getName(),
            '(', implode(", ", $args), ')</pre>';
    }
}

?>
```

(Just like above, every aspect file must contain exactly one class definition.)

Now you have to register the aspect in `debug/artifact.json` (similar to the
[copy generator](#copy-generator)):

```json
{
    "aspect": {
        "files": "DebugAspect.php"
    }
}
```

You may also want to specify the aspect kernel's target, class name or requiring
feature in the product line settings in `productLine.json`:

```json
{
    "aspect": {
        "target": "database/include/AspectKernel.class.php",
        "feature": "debug mode"
    }
}
```

Finally, you need to initialize the aspect kernel in your runtime code, e.g.:

```php
if (file_exists("AspectKernel.class.php"))
    require_once "AspectKernel.class.php";

if (class_exists("ApplicationAspectKernel"))
    ApplicationAspectKernel::getInstance()->init(array(
        "includePaths" => array(__DIR__)
    ));
```

For this to work, you also need to include [Go!
AOP](https://github.com/goaop/framework) in the generated product, e.g. using
this Composer file in your artifact:

```json
{
    "require": {
        "php": ">=5.3.0",
        "goaop/framework": "^2.1",
        "doctrine/annotations": "1.4.0"
    }
}
```

You may want to include it implicitly, or only if aspects are generated - this
is easily done with the [copy generator](#copy-generator).

To read more about aspects, click
[here](https://ekuiter.github.io/feature-php/classes/FeaturePhp.Generator.AspectGenerator.html).

---

That's it! You are now a feature-php expert :)

If you have any remarks or ideas on how to improve this guide, please [contact
me](mailto:info@elias-kuiter.de).