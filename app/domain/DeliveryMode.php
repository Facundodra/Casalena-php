<?php
declare(strict_types=1);

enum DeliveryMode: string
{
    case DELIVERY = 'DELIVERY';
    case PICKUP   = 'PICKUP';

    public function label(): string
    {
        return match ($this) {
            DeliveryMode::DELIVERY => 'Envío a domicilio',
            DeliveryMode::PICKUP   => 'Retiro en el local',
        };
    }
}
