<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
            $table->renameColumn('phone', 'mobile');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('username');
            $table->string('last_name')->nullable()->after('first_name');
        });

        $usedUsernames = [];

        DB::table('users')->orderBy('id')->get(['id', 'username'])->each(function ($user) use (&$usedUsernames) {
            $parts = preg_split('/\s+/', trim((string) $user->username), 2);
            $firstName = $parts[0] ?? 'User';
            $lastName = $parts[1] ?? null;
            $base = Str::lower(Str::slug((string) $user->username, '.')) ?: 'user'.$user->id;
            $username = $base;
            $suffix = 2;

            while (isset($usedUsernames[$username])) {
                $username = $base.'.'.$suffix++;
            }

            $usedUsernames[$username] = true;

            DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
            $table->unique('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['mobile']);
            $table->dropColumn(['first_name', 'last_name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->renameColumn('mobile', 'phone');
        });
    }
};
