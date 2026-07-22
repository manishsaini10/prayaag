<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $oldTables = ['roles', 'permissions', 'role_user', 'permission_role'];

        foreach ($oldTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasTable("_old_$table")) {
                DB::statement("RENAME TABLE `$table` TO `_old_$table`");
                echo "Renamed `$table` → `_old_$table`\n";
            }
        }
    }

    public function down(): void
    {
        $oldTables = ['roles', 'permissions', 'role_user', 'permission_role'];

        foreach ($oldTables as $table) {
            if (Schema::hasTable("_old_$table") && !Schema::hasTable($table)) {
                DB::statement("RENAME TABLE `_old_$table` TO `$table`");
            }
        }
    }
};
