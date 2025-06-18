<?php

namespace App\Filament\Resources\PaymentChannelResource\Pages;

use App\Filament\Resources\PaymentChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentChannel extends CreateRecord
{
    protected static string $resource = PaymentChannelResource::class;
}
