<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);

        // create roles and assign created permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo([Permission::all()]);

        $role = Role::create(['name' => 'technician']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'business']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'businessconsultant']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'photovoltaic']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'worker']);
        $role->givePermissionTo(Permission::all());

        $user1 = new User;
        $user1->name = 'Majid Fazal';
        $user1->email = 'admin@gmail.com';
        $user1->phone = '123456789';
        $user1->status = 1;
        $user1->password = Hash::make('admin123');
        $user1->orignalpass = 'admin123';
        $user1->save();
        $user1->assignRole('admin');

        // $user2 = new User;
        // $user2->name = 'technician';
        // $user2->email = 'technician@gmail.com';
        // $user2->phone = '123456789';
        // $user2->status = 1;
        // $user2->password = Hash::make('technician123');
        // $user2->save();
        // $user2->assignRole('technician');

        // $user3 = new User;
        // $user3->name = 'business';
        // $user3->email = 'business@gmail.com';
        // $user3->phone = '123456789';
        // $user3->status = 1;
        // $user3->password = Hash::make('business123');
        // $user3->save();
        // $user3->assignRole('business');

        // $user4 = new User;
        // $user4->name = 'business consultant';
        // $user4->email = 'businessconsultant@gmail.com';
        // $user4->phone = '123456789';
        // $user4->status = 1;
        // $user4->password = Hash::make('businessconsultant123');
        // $user4->save();
        // $user4->assignRole('businessconsultant');

        // $user5 = new User;
        // $user5->name = 'photovoltaic';
        // $user5->email = 'photovoltaic@gmail.com';
        // $user5->phone = '123456789';
        // $user5->status = 1;
        // $user5->password = Hash::make('photovoltaic123');
        // $user5->save();
        // $user5->assignRole('photovoltaic');

        // $user6 = new User;
        // $user6->name = 'user';
        // $user6->email = 'user@gmail.com';
        // $user6->phone = '123456789';
        // $user6->status = 1;
        // $user6->password = Hash::make('user123');
        // $user6->save();
        // $user6->assignRole('user');

        // $user7 = new User;
        // $user7->name = 'worker';
        // $user7->email = 'worker@gmail.com';
        // $user7->phone = '123456789';
        // $user7->status = 1;
        // $user7->password = Hash::make('worker123');
        // $user7->save();
        // $user7->assignRole('worker');

        // $business = new BusinessDetail();
        // $business->user_id = $user3->id;
        // $business->company_name = 'Business Company';
        // $business->company_type = 'Hydraulic';
        // $business->save();
    }
}
