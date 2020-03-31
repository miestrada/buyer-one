<?php

namespace App\Entity;

interface PaymentMethodInterface
{

    public function getType(): string;

    public function cast(): array;

}