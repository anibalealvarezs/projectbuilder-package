<?php

namespace Anibalealvarezs\Projectbuilder\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PbUserException extends HttpException
{
    public static function custom($statusCode, $message, $previous = null): self
    {
        return new static($statusCode, $message, $previous);
    }

    public static function notViewable(): self
    {
        return new static(403, 'You can\'t view this user.', null, []);
    }

    public static function notEditable(): self
    {
        return new static(403, 'You can\'t edit this user.', null, []);
    }

    public static function notDeletable(): self
    {
        return new static(403, 'You can\'t delete this user.', null, []);
    }

    public static function notSelectable(): self
    {
        return new static(403, 'You can\'t select this user.', null, []);
    }
}
