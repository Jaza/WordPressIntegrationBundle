<?php

namespace Jaza\WordPressIntegrationBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class WordPressIntegration
{
    private $request;

    private $router;

    private $wordpress_root;

    private $wordpress_base_url;

    private $wordpress_is_embedded;

    public function __construct(Request $request, Router $router, $wordpress_root, $wordpress_base_url, $wordpress_is_embedded)
    {
        $this->request = $request;
        $this->router = $router;
        $this->wordpress_root = $wordpress_root;
        $this->wordpress_base_url = $wordpress_base_url;
        $this->wordpress_is_embedded = $wordpress_is_embedded;
    }

    /**
     * Bootstraps WordPress using values from this app's config.
     */
    public function bootstrapWordPress()
    {
        global $wp_did_header;

        // Check that WordPress bootstrap config settings can be found.
        // If not, throw an exception.
        if (empty($this->wordpress_root)) {
            throw new \Exception('Missing setting \'wordpress_root\' in config');
        }
        elseif (empty($this->wordpress_base_url)) {
            throw new \Exception('Missing setting \'wordpress_base_url\' in config');
        }

        define('WP_USE_THEMES', true);

        if (!isset($wp_did_header)) {
            $wp_did_header = true;
            require_once($this->wordpress_root . '/wp-load.php');
            wp();
        }
    }

    /**
     * Gets a Symfony Response object for the specified content.
     * If set to embed output within WordPress, render the WordPress
     * template before preparing the Symfony Response object.
     *
     * @param $content
     *   Content string.
     * @param $head_title
     *   HTML head title (optional).
     *
     * @return
     *   Symfony Response object.
     */
    public function getResponse($content, $head_title = NULL)
    {
        if ($this->wordpress_is_embedded) {
            global $wp_symfony_override_title;

            if (!empty($head_title)) {
                $wp_symfony_override_title = $head_title;
            }

            ob_start();
            get_header();
            print $content;
            get_footer();
            $content = ob_get_contents();
            ob_end_clean();

            $content = str_replace('<body class="error404', '<body class="', $content);
        }

        return new Response($content);
    }
}
