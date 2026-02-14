<?php /** @noinspection ALL */

class FoolPhp {
    public function __call($name, $arguments) {
        return $this->{$arguments[0]}(str_replace('_', ' ', $name));
    }

    public function print($message) {
        echo $message . PHP_EOL;
    }
}

$php = new FoolPhp();
$php->Hello_World("print");
$php->Today_is_the_31st_of_January_2026("print");
