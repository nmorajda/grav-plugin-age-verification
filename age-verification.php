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
        $uri = $this->grav['uri'];
        $path = $uri->path();

        // Retrieve configuration settings
        $redirect_url     = $this->config->get('plugins.age-verification.redirect_url', 'https://www.google.com');
        $cookie_duration  = (int) $this->config->get('plugins.age-verification.cookie_duration', 2592000); // 30 days in seconds
        $cookie_name      = $this->config->get('plugins.age-verification.cookie_name', 'age_verified');
        $protected_paths  = $this->config->get('plugins.age-verification.protected_paths', []);
        $excluded_paths   = $this->config->get('plugins.age-verification.excluded_paths', []);

        // Determine if the current path should be protected
        $should_protect = false;
        if (!empty($protected_paths)) {
            foreach ($protected_paths as $protected_path) {
                if (strpos($path, $protected_path) === 0) {
                    $should_protect = true;
                    break;
                }
            }
        } else {
            $should_protect = true;
            foreach ($excluded_paths as $excluded_path) {
                if (strpos($path, $excluded_path) === 0) {
                    $should_protect = false;
                    break;
                }
            }
        }

        // If the current path should not be protected, do nothing
        if (!$should_protect) {
            return;
        }

        $request = $this->grav['request'];

        // Handle form submission
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            
            // Handle "Yes" button submission
            if (isset($post['age_verify']) && $post['age_verify'] === 'true') {
                // Set the age verification cookie
                setcookie($cookie_name, 'true', [
                    'expires'   => time() + $cookie_duration, // Cookie expiration time
                    'path'      => '/',
                    'secure'    => $this->grav['uri']->scheme(true) === 'https',
                    'httponly'  => true,
                    'samesite'  => 'Strict',
                ]);

                // Redirect back to the original page
                $redirect = $uri->route();
                $this->grav->redirect($redirect);
            }

            // Handle "No" button submission
            if (isset($post['age_verify_no']) && $post['age_verify_no'] === 'true') {
                // Redirect to the specified URL
                $this->grav->redirect($redirect_url);
            }
        }

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

        // Add assets
        if ($this->config->get('plugins.age-verification.built_in_css')) {
            $this->grav['assets']
                ->add('plugin://age-verification/assets/age-verification.css');
        }

        // Pass variables to the Twig template
        $this->grav['twig']->twig_vars['age_verification'] = [
            'age_verification'       => 'ok',
            'redirect_url' => $this->config->get('plugins.age-verification.redirect_url', 'https://www.google.com')
        ];
    }
}
