<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class AgeVerificationPlugin extends Plugin
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ];
    }

    public function onPluginsInitialized()
    {
        if (!$this->isAdmin()) {
            $this->enable([
                'onPagesInitialized'    => ['onPagesInitialized', 0],
                'onTwigSiteVariables'   => ['onTwigSiteVariables', 0],
            ]);
        }
    }

    public function onPagesInitialized(Event $event)
    {
        $request = $this->grav['request'];

        // Handle form submission
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if (isset($post['age_verify']) && $post['age_verify'] === 'true') {
                // Retrieve cookie settings from configuration
                $cookie_duration = (int) $this->config->get('plugins.age-verification.cookie_duration', 2592000); // Default 30 days in seconds
                $cookie_name = $this->config->get('plugins.age-verification.cookie_name', 'age_verified');

                // Set the age verification cookie
                setcookie($cookie_name, 'true', [
                    'expires'   => time() + $cookie_duration, // Cookie expiration time
                    'path'      => '/',
                    'secure'    => $this->grav['uri']->scheme(true) === 'https',
                    'httponly'  => true,
                    'samesite'  => 'Strict',
                ]);

                // Redirect back to the original page
                $redirect = $this->grav['uri']->route();
                $this->grav->redirect($redirect);
            }
        }

        // Retrieve cookie settings from configuration
        $cookie_name = $this->config->get('plugins.age-verification.cookie_name', 'age_verified');

        // Check if the age verification cookie is set
        if (!isset($_COOKIE[$cookie_name]) || $_COOKIE[$cookie_name] !== 'true') {
            // Replace the current page with the age verification page
            $page = $this->grav['page'];
            $page->template('age-verification');
            $page->content('');
        }
    }

    public function onTwigTemplatePaths()
    {
        // Add the plugin's templates directory to Twig's path
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        // Pass variables to the Twig template
        $this->grav['twig']->twig_vars['age_verification'] = [
            'age_verification'       => 'ok',
        ];
    }
}
