<?php

namespace Phoxx\Core\Mailer;

interface Mailer
{
    public function send(Mail $mail): void;
}
