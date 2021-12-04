<?php

use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
            [
				'id' => 1, 
				'name' => 'Admin', 
				'email' => 'master@erp.com', 
				'password' => \Hash::make('secret'), 
				'role_id' => 1, 
				'remember_token' => ''
			],

        ];

        foreach ($items as $item) {
            \App\User::create($item);
        }
    }
}
