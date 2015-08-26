<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as sfController;

/**
 * Base class for all controllers
 */
abstract class BaseController extends sfController
{
    /**
     * Return a database connection.
     * Shorthand to get the database_connection service
     */
    protected function db()
    {
        return $this->get("database_connection");
    }

    /**
     * Return the current request.
     * Shorthand to call the request_stack service's getCurrentRequest.
     */
    protected function request()
    {
        return $this->get("request_stack")->getCurrentRequest();
    }

    private function log($level, $args)
    {
        if(count($args)>1){
            $msg=call_user_func_array("sprintf", $args);
        }else{
            $msg=$args[0];
        }
        $this->get("logger")->$level($msg);
    }

    /**
     * Format and log at debug level.
     * Shorthand to format the parameters using sprintf, then logging it to the logger service at debug level.
     */
    protected function debug($format)
    {
        $this->log("debug", func_get_args());
    }

    /**
     * Format and log at info level.
     * Shorthand to format the parameters using sprintf, then logging it to the logger service at info level.
     */
    protected function info($format)
    {
        $this->log("info", func_get_args());
    }

    /**
     * Return the flash bag.
     * Shorthand to get the session's flashbag.
     */
    protected function flash()
    {
        return $this->get("session")->getFlashBag();
    }

    /**
     * Return the session service
     */
    protected function session()
    {
        return $this->get("session");
    }
}
