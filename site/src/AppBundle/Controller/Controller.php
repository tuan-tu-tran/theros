<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as sfController;

/**
 * Base class for all controllers
 */
abstract class Controller extends sfController
{
    /**
     * Return a database connection.
     * Shorthand to get the database_connection service
     */
    protected function db()
    {
        return $this->get("database_connection");
    }
}
