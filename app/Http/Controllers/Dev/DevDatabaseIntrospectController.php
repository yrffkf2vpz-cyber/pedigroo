<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevDatabaseIntrospectController extends Controller
{
    /**
     * List all tables in the database.
     *
     * GET /api/dev/db/tables
     */
    public function tables()
    {
        $tables = DB::select('SHOW TABLES');

        $result = [];
        foreach ($tables as $row) {
            $result[] = array_values((array)$row)[0];
        }

        return response()->json([
            'tables' => $result,
        ]);
    }

    /**
     * List columns of a table.
     *
     * GET /api/dev/db/columns?table=pd_dogs
     */
    public function columns(Request $request)
    {
        $table = $request->get('table');

        if (!$table) {
            return response()->json(['error' => 'table is required'], 400);
        }

        $columns = DB::select("SHOW COLUMNS FROM `$table`");

        return response()->json([
            'table'   => $table,
            'columns' => $columns,
        ]);
    }

    /**
     * List indexes of a table.
     *
     * GET /api/dev/db/indexes?table=pd_dogs
     */
    public function indexes(Request $request)
    {
        $table = $request->get('table');

        if (!$table) {
            return response()->json(['error' => 'table is required'], 400);
        }

        $indexes = DB::select("SHOW INDEX FROM `$table`");

        return response()->json([
            'table'   => $table,
            'indexes' => $indexes,
        ]);
    }

    /**
     * List foreign keys of a table.
     *
     * GET /api/dev/db/foreign-keys?table=pd_dogs
     */
    public function foreignKeys(Request $request)
    {
        $table = $request->get('table');

        if (!$table) {
            return response()->json(['error' => 'table is required'], 400);
        }

        $dbName = DB::getDatabaseName();

        $foreignKeys = DB::select("
            SELECT
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName, $table]);

        return response()->json([
            'table'       => $table,
            'foreignKeys' => $foreignKeys,
        ]);
    }

    /**
     * Check if a table exists.
     *
     * GET /api/dev/db/exists/table?name=pd_dogs
     */
    public function tableExists(Request $request)
    {
        $name = $request->get('name');

        if (!$name) {
            return response()->json(['error' => 'name is required'], 400);
        }

        $exists = DB::select("SHOW TABLES LIKE ?", [$name]);

        return response()->json([
            'table'  => $name,
            'exists' => !empty($exists),
        ]);
    }

    /**
     * Check if a column exists in a table.
     *
     * GET /api/dev/db/exists/column?table=pd_dogs&column=name
     */
    public function columnExists(Request $request)
    {
        $table  = $request->get('table');
        $column = $request->get('column');

        if (!$table || !$column) {
            return response()->json(['error' => 'table and column are required'], 400);
        }

        $columns = DB::select("SHOW COLUMNS FROM `$table` LIKE ?", [$column]);

        return response()->json([
            'table'  => $table,
            'column' => $column,
            'exists' => !empty($columns),
        ]);
    }
}