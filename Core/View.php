<?php

namespace Core;

/**
 * View 
 * 
 * PHP version 8.1.1
 */
class View
{
    /**
     * Render a view file
     * 
     * @param string $view The view file
     * 
     * @param array $args Associative array of data to display in the view (optional)
     * 
     * @return void
     */

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view"; // Relative to Core directory

        $current_user = \App\Auth::getUser();

        $flash_messages = \App\Flash::getMessages();

        if (is_readable($file)) {
            require $file;
        } else {
            // echo "$file not found";
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     * 
     * @param string $template The template file
     * @param array $args Associative array of datta to display in the view (optional)
     * 
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            // Twig uses a loader (\Twig\Loader\ArrayLoader) to locate templates
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            // $loader = new \Twig\Loader\FilesystemLoader(C:\xampp\htdocs\PHP\Other\MVC/App/Views');

            //An environment (\Twig\Environment) to store templates configuration
            $twig = new \Twig\Environment($loader, [
                'debug' => true
            ]);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        echo $twig->render($template, $args);
    }
}
