<?php

namespace system\lib\slim;

/**
 * Session middleware
 *
 * This class is meant to provide a easy way to manage sessions with framework,
 * using the PHP built-in (native) sessions but also allowing to manipulate the
 * session variables via same app instance, by registering a container to the
 * helper class that ships with this package. As a plus, you can set a lifetime
 * for a session and it will be updated after each user activity or interaction
 * like an 'autorefresh' feature.
 *
 * Keep in mind this relies on PHP native sessions, so for this to work you
 * must have that enabled and correctly working.
 *
 * @package Slim\Middleware
 * @author  Maes Jerome
 */
class SlimSessionMiddleware extends \Slim\Middleware
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = array())
    {
        $defaults = array(
            'lifetime' => '20 minutes',
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'httponly' => false,
            'name' => 'slim_session',
            'autorefresh' => false
        );
        $settings = array_merge($defaults, $settings);
        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1);
        ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
    }

    /**
     * Call
     */
    public function call()
    {
        $this->startSession();
        $this->next->call();
    }

    /**
     * Start session
     */
    protected function startSession()
    {
        if (session_id()) {
            return;
        }

        $settings = $this->settings;
        $name = $settings['name'];

        session_set_cookie_params(
            $settings['lifetime'],
            $settings['path'],
            $settings['domain'],
            $settings['secure'],
            $settings['httponly']
        );
        session_name($name);
        session_cache_limiter(false);
        session_start();

        if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
            setcookie(
                $name,
                $_COOKIE[$name],
                time() + $settings['lifetime'],
                $settings['path'],
                $settings['domain'],
                $settings['secure'],
                $settings['httponly']
            );
        }
    }
}
