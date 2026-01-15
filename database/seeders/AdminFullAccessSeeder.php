<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;
use App\Models\UserType;

class AdminFullAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = UserType::find(1); // Assuming Admin role ID is 1

        if ($adminRole) {
            $modules = config('rbac.modules', []);

            foreach ($modules as $moduleKey => $moduleData) {
                if (isset($moduleData['permissions']) && is_array($moduleData['permissions'])) {
                    foreach ($moduleData['permissions'] as $permission) {
                        RolePermission::updateOrCreate(
                            [
                                'user_type_id' => $adminRole->id,
                                'module' => $moduleKey,
                                'ability' => $permission,
                            ],
                            [
                                // No need to update any other fields
                            ]
                        );
                    }
                }
            }
        }
    }
}
