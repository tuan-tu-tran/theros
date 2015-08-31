<?php

namespace AppBundle\Controller;

/**
 * Base class for all controllers that exposes app specific methods
 */
abstract class Controller extends BaseController
{
    /**
     * Return the user stored in session
     */
    protected function user()
    {
        return $this->session()->get("user");
    }

    /**
     * Return the current schoolyear (description).
     * Shorthand to get the schoolyear parameter
     */
    protected function getSchoolYear()
    {
        return $this->getParameter("schoolyear");
    }
}
