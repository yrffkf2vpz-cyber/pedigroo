<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DevModuleGeneratorController extends Controller
{
    /**
     * Generate a basic module skeleton:
     * - Service
     * - Admin Controller
     * - Migration
     *
     * POST /api/dev/module/generate
     * body: {
     *   "name": "BreedingLicense",
     *   "table": "pd_breeding_licenses"
     * }
     */
    public function generate(Request $request)
    {
        $name  = $request->get('name');
        $table = $request->get('table');

        if (!$name || !$table) {
            return response()->json(['error' => 'name and table are required'], 400);
        }

        $basePath = base_path();

        // 1) Service
        $serviceDir  = $basePath . '/app/Services/' . $name;
        $servicePath = $serviceDir . '/' . $name . 'Service.php';

        if (!File::isDirectory($serviceDir)) {
            File::makeDirectory($serviceDir, 0755, true);
        }

        $serviceContent = $this->generateServiceContent($name, $table);
        File::put($servicePath, $serviceContent);

        // 2) Admin Controller
        $controllerDir  = $basePath . '/app/Http/Controllers/Admin';
        $controllerPath = $controllerDir . '/' . $name . 'AdminController.php';

        if (!File::isDirectory($controllerDir)) {
            File::makeDirectory($controllerDir, 0755, true);
        }

        $controllerContent = $this->generateAdminControllerContent($name, $table);
        File::put($controllerPath, $controllerContent);

        // 3) Migration
        $migrationDir = $basePath . '/database/migrations';
        if (!File::isDirectory($migrationDir)) {
            File::makeDirectory($migrationDir, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $migrationFile = $migrationDir . '/' . $timestamp . '_create_' . $table . '_table.php';

        $migrationContent = $this->generateMigrationContent($table);
        File::put($migrationFile, $migrationContent);

        return response()->json([
            'status' => 'generated',
            'service' => str_replace($basePath . '/', '', $servicePath),
            'controller' => str_replace($basePath . '/', '', $controllerPath),
            'migration' => str_replace($basePath . '/', '', $migrationFile),
        ]);
    }

    protected function generateServiceContent(string $name, string $table): string
    {
        $class = $name . 'Service';

        return <<<PHP
<?php

namespace App\Services\\$name;

use Illuminate\Support\Facades\DB;

class $class
{
    protected string \$table = '$table';

    public function all(): array
    {
        return DB::table(\$this->table)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    public function find(int \$id): ?object
    {
        return DB::table(\$this->table)->where('id', \$id)->first();
    }

    public function create(array \$data): int
    {
        return DB::table(\$this->table)->insertGetId([
            ...\$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int \$id, array \$data): void
    {
        DB::table(\$this->table)
            ->where('id', \$id)
            ->update([
                ...\$data,
                'updated_at' => now(),
            ]);
    }

    public function delete(int \$id): void
    {
        DB::table(\$this->table)->where('id', \$id)->delete();
    }
}

PHP;
    }

    protected function generateAdminControllerContent(string $name, string $table): string
    {
        $class = $name . 'AdminController';
        $serviceClass = "App\\Services\\$name\\{$name}Service";

        return <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use $serviceClass;

class $class extends Controller
{
    protected {$name}Service \$service;

    public function __construct({$name}Service \$service)
    {
        \$this->service = \$service;
    }

    public function index()
    {
        return response()->json([
            'data' => \$this->service->all(),
        ]);
    }

    public function show(int \$id)
    {
        \$item = \$this->service->find(\$id);

        if (!\$item) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(['data' => \$item]);
    }

    public function store(Request \$request)
    {
        \$data = \$request->all();
        \$id = \$this->service->create(\$data);

        return response()->json([
            'status' => 'created',
            'id'     => \$id,
        ], 201);
    }

    public function update(Request \$request, int \$id)
    {
        \$data = \$request->all();
        \$this->service->update(\$id, \$data);

        return response()->json([
            'status' => 'updated',
            'id'     => \$id,
        ]);
    }

    public function destroy(int \$id)
    {
        \$this->service->delete(\$id);

        return response()->json([
            'status' => 'deleted',
            'id'     => \$id,
        ]);
    }
}

PHP;
    }

    protected function generateMigrationContent(string $table): string
    {
        $className = 'Create' . Str::studly($table) . 'Table';

        return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('$table', function (Blueprint \$table) {
            \$table->id();
            // TODO: add your columns here
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('$table');
    }
};

PHP;
    }
}