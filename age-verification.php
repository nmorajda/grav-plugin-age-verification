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
                'onPagesInitialized' => ['onPagesInitialized', 0],
                'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            ]);
        }
    }

    public function onPagesInitialized(Event $event)
    {
        $request = $this->grav['request'];

        // Obsługa przesłania formularza
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if (isset($post['age_verify']) && $post['age_verify'] === 'true') {
                // Ustawienie ciasteczka potwierdzającego pełnoletniość
                $cookie_expiration = 30; // Domyślnie 30 dni
                setcookie('age_verified', 'true', [
                    'expires' => time() + (86400 * $cookie_expiration), // Czas ważności
                    'path' => '/',
                    'secure' => $this->grav['uri']->scheme(true) === 'https',
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]);

                // Przekierowanie z powrotem na oryginalną stronę
                $redirect = $this->grav['uri']->route();
                $this->grav->redirect($redirect);
            }
        }

        // Sprawdzenie, czy ciasteczko potwierdzające pełnoletniość jest ustawione
        if (!isset($_COOKIE['age_verified']) || $_COOKIE['age_verified'] !== 'true') {
            // Zamiana bieżącej strony na stronę potwierdzenia pełnoletności
            $page = $this->grav['page'];
            $page->template('age-verification');
            $page->content('');
        }
    }

    public function onTwigTemplatePaths()
    {
        // Dodanie ścieżki do szablonów pluginu
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        // Przekazanie zmiennych do szablonu Twig
        $this->grav['twig']->twig_vars['age_verification'] = [
            'verification' => 'ok',
        ];
    }
}
