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
                // Set the age verification cookie
                $cookie_expiration = 30; // Default to 30 days
                setcookie('age_verified', 'true', [
                    'expires'   => time() + (86400 * $cookie_expiration), // Cookie expiration time
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

        // Check if the age verification cookie is set
        if (!isset($_COOKIE['age_verified']) || $_COOKIE['age_verified'] !== 'true') {
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
