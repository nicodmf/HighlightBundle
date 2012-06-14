Presentation
============
This bundle transforms a part of text representing a source code in html formated text. It provides 5 highlighters :

- pygment
- geshi
- highlight
- http request to appspot
- http request to hiliteme

Two caches mechanisms permit to conserve highlighted informations about languages and work made by the highlighter, moreover symfony integrated cache.

Installation
============
Standard symfony installation :

- download and decompress the bundle package in `vendor/bundles/Highlight`
- if git is installed, to perform this action, enter the command :

```
git submodule add git://github.com/nicodmf/HighlightBundle.git vendor/bundles/Highlight`
```
    
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

###Test and api###
It is possible to add routing properties to test and/or transform this bundle in highlighting server.

 - Add imported route in routing configuration file.

```
import:
   resource: @HighlightBundle/Resources/config/routing.yml
```

The new routes are accessible in the url : `http://[site]/highlight/`. The web service `http://[site]/highlight/api` or with prefix if it has been defined.

Usage
=====
Highlight can be use in twig or in phptemplates.
Css
---
Except for hiliteme which not propose to transform text with adding css classes and make transform by adding css properties, the colors are configurable by the css file included in the `Ressource/public` directory. The colors aren't available if you don't add stylesheet in your template. Sorry for this inconvenient way, but at this moment symfony don't propose core mechanisms to link css in submodules.

``` twig
{% stylesheets '@HighlightBundle/Resources/public/*.css' output='css/a.css' %}
       <link href="{{ asset_url }}" type="text/css" rel="stylesheet" />
{% endstylesheets %}
```

Options
-------
The default options can be overriden by adding options after importation of the bundle config.
An example in app/config/config.yml:

``` yaml
highlight:
   # Each provider in this list are use on after other
   # if language given in template ins't allowed
   providers: [ geshi, httphiliteme, httpappspot, highlight, pygment ]
   # All globals options can be rewrite in a specific provider
   # except cssclass who just added
   globals:
       linenos: true
       blockstyles: ""
       cssclass: highlight

   highlight:
       linenos: true
       blockstyles: ""
       cssclass: highlight

   pygment:
       linenos: true
       blockstyles: ""
       cssclass: pygment

   geshi:
       linenos: false
       # Two possibilities fancy or normal
       linestyle: normal
       cssclass: geshi

   #line number not available with appspot
   httpappspot:
       blockstyles: "overflow:auto;color:white !important;"
       cssclass: pygment appspot

   httphiliteme:
       linenos: false
       #One of : colorful default emacs friendly fruity manni monokai murphy native pastie perldoc tango trac vs
       style: pastie
       #Additionnal css directive for div block
       blockstyles: "overflow:auto;color:white !important;"
       cssclass: pygment hiliteme
       
services:
    highlight.configuration:
        class: Highlight\Bundle\HighlightBundle
        tags:
            - { name: configuration }
    highlight.twig.extension:
        class: Highlight\Bundle\Extension\TwigExtension
        tags:
            - { name: twig.extension }
        arguments: [@translator, @kernel, @service_container ]
        #arguments: [@translator, @templating.globals, @templating.helper.assets ]
```

In Twig
-------
Highlight can be used as filter, function or parser

### Filter
As a filter, highlight take a defined string or a defined string variable, the highlighter to use is optional :

```
{{ aDefinedStringVariable|highlight 'php' ['pygment'] }}
```

### Funtion
The function work with same purpose, with another syntax :

```
{{ highlight( aDefinedStringVariable, 'php'[, 'pygment']) }}
```

### Block parser
The parser is simply to use, because you don't have obligation to defined a variable. The code wich would be transformate is beetween standard twig tag. The highlighter is always optional :

```
{% highlight 'php' ['pygment'] %}
<?php echo "Bonjour à tous"; ?>
{% endhighlight %}
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
 - Update the Configuration.php to added the new parameters

