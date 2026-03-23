<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Str;

class PedrooCodeGenerator
{
    /**
     * 🔥 SERVICE GENERÁLÁS
     */
    public function generateService(string $serviceName): string
    {
        $class = Str::replaceLast('.php', '', $serviceName);

        return <<<PHP
<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class {$class}
{
    public function handle(array \$data): mixed
    {
        // TODO: implement service logic
        return null;
    }
}

PHP;
    }

    /**
     * 🔥 CONTROLLER GENERÁLÁS
     */
    public function generateController(string $controllerName): string
    {
        $class = Str::replaceLast('.php', '', $controllerName);

        return <<<PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class {$class} extends Controller
{
    public function index()
    {
        return response()->json(['message' => '{$class} index']);
    }

    public function store(Request \$request)
    {
        return response()->json(['message' => '{$class} store']);
    }
}

PHP;
    }

    /**
     * 🔥 ROUTE GENERÁLÁS
     */
    public function generateRoute(string $instruction): string
    {
        // Példa: "Add route for history promotion"
        $slug = Str::slug($instruction);
        $url = "/{$slug}";

        return <<<ROUTE
Route::get('{$url}', function () {
    return response()->json(['route' => '{$url}']);
});
ROUTE;
    }

    /**
     * 🔥 MIGRATION GENERÁLÁS
     */
    public function generateMigration(string $instruction): array
    {
        $table = Str::snake(Str::replace('migration', '', $instruction));
        $table = trim($table, '_ ');

        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$table}_table.php";

        $contents = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};

PHP;

        return [
            'filename' => $filename,
            'contents' => $contents,
        ];
    }

    /**
     * 🔥 CONTROLLER NÉV KINYERÉSE
     */
    public function extractControllerName(string $instruction): string
    {
        // Példa: "Generate controller for history"
        $name = Str::studly(Str::replace('Generate controller for ', '', $instruction));

        return "{$name}Controller.php";
    }
}