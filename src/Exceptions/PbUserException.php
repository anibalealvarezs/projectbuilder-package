<?php

namespace Anibalealvarezs\Projectbuilder\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PbUserException extends HttpException
{
    /**
     * Scope a query to only include popular users.
     *
     * @param $statusCode
     * @param $message
     * @param null $previous
     * @return PbUserException
     */
    public static function custom($statusCode, $message, $previous = null): self
    {
        return new static($statusCode, $message, $previous);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUserException
     */
    public static function notViewable(): self
    {
        return new static(403, 'You can\'t view this user.', null, []);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUserException
     */
    public static function notEditable(): self
    {
        return new static(403, 'You can\'t edit this user.', null, []);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUserException
     */
    public static function notDeletable(): self
    {
        return new static(403, 'You can\'t delete this user.', null, []);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUserException
     */
    public static function notSelectable(): self
    {
        return new static(403, 'You can\'t select this user.', null, []);
    }
}
