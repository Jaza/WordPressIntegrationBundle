Symfony2 WordPress Integration Bundle
==================================

This bundle lets you render a Symfony2 web app via WordPress.

## Installation

1  Add to the 'require' section of composer.json:  

``` 
    "require" : {
        "jaza/wordpress-integration-bundle": "1.0.*@dev",
    }
``` 
 
2  Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Jaza\WordPressIntegrationBundle\JazaWordPressIntegrationBundle(),
    );
```

## Configuration

1  Add required config values to 'parameters.yml' file (or equivalent):

``` 
parameters:
    # ...
    wordpress_root: /path/to/wordpress
    wordpress_base_url: 'http://wordpress.baseurl'
```

2  Add various optional config values to 'parameters.yml' file
   (or equivalent):

``` 
parameters:
    # ...
    wordpress_is_embedded: true
```

## Usage

Create a controller in your bundle

``` php

namespace YOURNAME\YOURBUNDLE\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        $title = 'Foo hoo';

        // Get the WordPress integration service and bootstrap WordPress
        $wordPressIntegration = $this->container->get('wordpress_integration');
        $wordPressIntegration->bootstrapWordPress();

        // Easily check if the current response will be rendered via
        // WordPress or not - e.g. might have conditional template logic
        // based on this.
        $embedded = $this->container->getParameter('wordpress_is_embedded');

        $engine = $this->container->get('templating');

        // Render the Symfony template output and store it in a variable
        // here, rather than returning the response directly to
        // Symfony as you'd normally do.
        $content = $engine->render('FooBundle:Default:index.html.twig', array(
            'title' => $title,
            'embedded' => $embedded,
        ));

        // Return a Symfony Response object - whether the content in
        // the response is output via WordPress or not depends on the
        // 'wordpress_is_embedded' config value.
        return $wordPressIntegration->getResponse($content);
    }
}

```
