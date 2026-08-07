<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('two_factor_codes', function (Blueprint $table) {
                    $table->id();

            // Un usuario solo debería tener un código vigente a la vez;
            // por lo que solo se actualiza el código y la fecha de expiración,
            // en vez de insertar uno nuevo cada vez.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Código de 6 digitos, como string para no perder ceros a la izquierda.
            $table->string('code', 6);

            // Canal por el que se envio (email | sms)
            $table->string('channel', 20);

            // Momento en que el código deja de ser válido.
            $table->timestamp('expires_at');

            $table->timestamps();

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_codes');
    }
};
