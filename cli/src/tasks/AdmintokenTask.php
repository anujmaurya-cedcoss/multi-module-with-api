<?php

declare(strict_types=1);

namespace MyApp\Tasks;

use Phalcon\Cli\Task;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;

class AdmintokenTask extends Task
{
    public function mainAction()
    {
        // Defaults to 'sha512'
        $signer = new Hmac();

        // Builder object
        $builder = new Builder($signer);
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $builder
            ->setSubject('admin')
            ->setPassphrase($passphrase);
        $tokenObject = $builder->getToken();
        echo $tokenObject->getToken();
    }
}
