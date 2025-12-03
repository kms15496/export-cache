
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cached_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['report_type','start_date','end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cached_reports');
    }
};
