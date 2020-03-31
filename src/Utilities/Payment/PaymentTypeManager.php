<?php

namespace App\Utilities\Payment;

use App\Utilities\Payment\Type\PaymentTypeInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PaymentTypeManager
{

    public static array $types = [];

    public function __construct()
    {

    }

    public function __invoke()
    {
        die('invoke2');
    }

    public function addType(PaymentTypeInterface $type)
    {
        self::$types[$type->getType()] = $type;
    }

    public function getType(string $alias)
    {
        if (array_key_exists($alias, self::getTypes()))
            return self::$types[$alias];
    }

    public function getTypes(): array
    {
        return self::$types;
    }

    public static function getTypeChoices(): array
    {
        return array_keys(self::$types);
    }
}