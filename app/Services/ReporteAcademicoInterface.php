<?php

declare(strict_types=1);

namespace App\Services;

interface ReporteAcademicoInterface
{
    /** @return array<int, array<string, mixed>> */
    public function asignacionesConDetalle(): array;
}