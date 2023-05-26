<?php

declare(strict_types=1);

namespace MyApp\Tasks;

use Phalcon\Cli\Task;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;

class MainTask extends Task
{
    public function mainAction($mail, $password, $role)
    {
        $arr = [
            "mail" => $mail,
            "password" => $password,
            "token" => $this->generateToken($role)
        ];
        $ch = curl_init();
        $url = "http://192.168.80.4/register?bearer=admin";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        if ($output) {
            echo "Registered Successfully !";
        } else {
            echo "<h3>There was some error</h3>";
            die;
        }
    }

    public function generateToken($role)
    {
        // Defaults to 'sha512'
        $signer = new Hmac();

        // Builder object
        $builder = new Builder($signer);
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $builder
            ->setSubject($role)
            ->setPassphrase($passphrase);
        $tokenObject = $builder->getToken();
        return $tokenObject->getToken();
    }
}
