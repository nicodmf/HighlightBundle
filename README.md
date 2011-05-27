Presentation
============
This bundle transforme a part of text representing a source code in html formated text. It provides 5 highlighter :

- pygment
- geshi
- highlight
- http request to appspot
- http request to hiliteme

Two caches mecanismes permit to conserve highlighted informations about languages and work made by the highlighter, moreover symfony integrate cache.

Installation
============
Standard symfony installation :

- download and decompresse the bundle package in vendor/bundles/Highlight (if git installed the command  `git submodule add git://github.com/nicodmf/HighlightBundle.git vendor/bundles/Highlight` perform this action)
- add namespace in `app/autoload.php` :

``` php
<?php
$loader->registerNamespaces(array(
    //...
    'Highlight'        => __DIR__.'/../vendor/bundles',
    //...
));
```

- add class creation in `app/AppKernel.php`

``` php
    <?php
    //...
    public function registerBundles()
    {
        $bundles = array(
            //...
            new Highlight\Bundle\HighlightBundle(),
            //...
        );
    }
    //...
```

- add config import in app/config/config.yml

``` yaml
imports:
  #...
  - { resource: @HighlightBundle/Resources/config/config.yml }
  #...
```

Usage
=====
Highlight can be use in twig or in phptemplates.
Css
---
Except for hiliteme wich not propose to transform text with adding css classes and make transform by adding css properties, the colors are configurable by the css file included in the `Ressource/public` directory. The colors aren't available if you don't add stylesheet in your template. Sorry for this inconvenient way, but at this moment symfony don't propose core mecanisme to link css in submodules.

``` twig
{% stylesheets '@HighlightBundle/Resources/public/*.css' output='css/a.css' %}
       <link href="{{ asset_url }}" type="text/css" rel="stylesheet" />
{% endstylesheets %}
```

Options
-------
The default options can be overide by adding options after importation of the bundle config.
An exemple in app/config/config.yml:

``` yaml
highlight:
    #This is the list of providers use one after other if language isn't available
    providers: [ httphiliteme, geshi, httpappspot, highlight, pygment ]
    #The global options for all providers
    globals:
       #Add line number if the provider in use can display them
       linenos: true
       cssclass: highlight
    #The specific option for the httphiliteme provider
    httphiliteme:
       blockstyles: "overflow:auto;color:white !important; border-radius:10px;"
```

In Twig
-------
Highlight can be use as filter, function or parser

### Filter
As a filter, highlight take a defined string or a defined string variable, the highlighter to use is optional :

```
{{ aDefinedStringVariable|highlight php pygment }}
```

### Funtion
The function work with same purpose, with another syntax :

```
{{ highlight( aDefinedStringVariable, php, pygment) }}
```

### Block parser
The parser is simply to use, because you don't have obligation to defined a variable. The code wich would be transformate is beetween standard twig tag. The highlighter is always optional :

```
{{ highlight php pygment }}
<?php echo "Bonjour à tous"; ?>
{{ endhighlight }}
```

Extends
=======
Provider
--------
It is very simple to add another provider if you want to use one not listed here.

In the provider directory :

 - Create a provider class in providers
 - Add its creation in factory.php
In the DependancyInjection directory
 - Update the config to added the new parameters

Usage
-----
It is possible to add routing properties to transform this bundle in highlighting server.

 - Add imported route in routing configuration file.
 
```
import:
   resource: @HighlightBundle/Resources/config/routing.yml
```

The news routes are accessible inthe url : `http://[site]/highlight/`. The web service `http://[site]/highlight/api`
