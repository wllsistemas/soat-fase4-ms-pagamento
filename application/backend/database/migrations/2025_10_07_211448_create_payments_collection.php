<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('mercado_pago_pagamentos', function ($collection) {
            $collection->index('ordem_uuid', 'ordem_uuid_idx');
            $collection->index('pagamento_uuid', 'pagamento_uuid_idx');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->drop('mercado_pago_pagamentos');
    }
};
