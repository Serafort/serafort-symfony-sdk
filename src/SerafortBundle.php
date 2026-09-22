<?php

declare(strict_types=1);

namespace Serafort\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SerafortBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
