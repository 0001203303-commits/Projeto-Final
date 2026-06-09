<?php
// database/migrations/create_triagem_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabela de Categorias (ex: Problemas Respiratórios, Dores...)
        Schema::create('triagem_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug'); // ex: 'problemas-respiratorios'
            $table->string('icon')->default('wind');
            $table->string('css_class')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('triagem_categorias')->onDelete('cascade'); 
            // parent_id serve para fazer a subcategoria apontar para a categoria pai
            $table->timestamps();
        });

        // Tabela de Perguntas Clínicas
        Schema::create('triagem_perguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('triagem_categorias')->onDelete('cascade');
            $table->text('texto_pergunta');
            $table->integer('score_manchester');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triagem_perguntas');
        Schema::dropIfExists('triagem_categorias');
    }
};

?>