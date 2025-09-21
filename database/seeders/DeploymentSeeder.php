<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;
use App\Models\Category;
use App\Models\Product;

class DeploymentSeeder extends Seeder
{
    /**
     * Run the database seeds for deployment.
     */
    public function run(): void
    {
        // Clear all tables except preserved ones
        $this->clearDatabase();
        
        // Create admin user
        $this->createAdminUser();
        
        // Create temporary staff and member users
        $this->createTemporaryUsers();
        
        // Sync users to members
        $this->syncUsersToMembers();
        
        // Seed basic categories if empty
        $this->seedBasicCategories();
        
        // Seed sample products if empty
        $this->seedSampleProducts();
    }

    /**
     * Clear database except preserved tables
     */
    private function clearDatabase(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        
        // Tables to preserve
        $preservedTables = [
            'categories',
            'migrations', 
            'products',
            'units'
        ];
        
        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$databaseName}"};
            
            // Skip preserved tables and system tables
            if (!in_array($tableName, $preservedTables) && !str_starts_with($tableName, 'information_schema')) {
                try {
                    DB::table($tableName)->truncate();
                    $this->command->info("Cleared table: {$tableName}");
                } catch (\Exception $e) {
                    $this->command->warn("Could not clear table {$tableName}: " . $e->getMessage());
                }
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Database cleared successfully (preserved: ' . implode(', ', $preservedTables) . ')');
    }

    /**
     * Create the system administrator
     */
    private function createAdminUser(): void
    {
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'mrcabandez@gmail.com',
            'mobile_number' => '09177260180',
            'password' => Hash::make('!@#123123'),
            'role' => 'Admin',
            'status' => 'Active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Created admin user: ' . $admin->email);
    }

    /**
     * Create temporary staff and member users
     */
    private function createTemporaryUsers(): void
    {
        // Create Staff users
        $staffUsers = [
            [
                'name' => 'Staff User 1',
                'email' => 'staff1@ebili.com',
                'mobile_number' => '09171234567',
                'password' => Hash::make('password123'),
                'role' => 'Staff',
                'status' => 'Active',
            ],
            [
                'name' => 'Staff User 2',
                'email' => 'staff2@ebili.com',
                'mobile_number' => '09171234568',
                'password' => Hash::make('password123'),
                'role' => 'Staff',
                'status' => 'Active',
            ]
        ];

        foreach ($staffUsers as $staffData) {
            $staff = User::create(array_merge($staffData, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $this->command->info('Created staff user: ' . $staff->email);
        }

        // Create Member users
        $memberUsers = [
            [
                'name' => 'Member User 1',
                'email' => 'member1@ebili.com',
                'mobile_number' => '09181234567',
                'password' => Hash::make('password123'),
                'role' => 'Member',
                'status' => 'Active',
            ],
            [
                'name' => 'Member User 2',
                'email' => 'member2@ebili.com',
                'mobile_number' => '09181234568',
                'password' => Hash::make('password123'),
                'role' => 'Member',
                'status' => 'Active',
            ],
            [
                'name' => 'Member User 3',
                'email' => 'member3@ebili.com',
                'mobile_number' => '09181234569',
                'password' => Hash::make('password123'),
                'role' => 'Member',
                'status' => 'Active',
            ]
        ];

        foreach ($memberUsers as $memberData) {
            $member = User::create(array_merge($memberData, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $this->command->info('Created member user: ' . $member->email);
        }
    }

    /**
     * Sync users to members table
     */
    private function syncUsersToMembers(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Check if user already has a member_id assigned
            if (!$user->member_id) {
                // Create member record first
                $memberData = [
                    'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                    'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                    'birthday' => '1990-01-01', // Default birthday
                    'mobile_number' => $user->mobile_number,
                    'status' => $user->status,
                    'role' => strtolower($user->role),
                    'address' => 'Default Address',
                    'occupation' => 'Not Specified',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];

                // Add role-specific data
                if ($user->role === 'Admin') {
                    $memberData['sponsor_id'] = null;
                } else {
                    // Find admin member to use as sponsor
                    $adminMember = Member::where('role', 'admin')->first();
                    $memberData['sponsor_id'] = $adminMember ? $adminMember->id : null;
                }

                $member = Member::create($memberData);
                
                // Update user with member_id
                $user->update(['member_id' => $member->id]);
                
                $this->command->info('Synced user to member: ' . $user->email . ' (Member ID: ' . $member->id . ')');
            } else {
                $this->command->info('User already has member record: ' . $user->email);
            }
        }
    }

    /**
     * Seed basic categories if table is empty
     */
    private function seedBasicCategories(): void
    {
        if (Category::count() == 0) {
            $categories = [
                ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
                ['name' => 'Clothing', 'description' => 'Apparel and fashion items'],
                ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies'],
                ['name' => 'Health & Beauty', 'description' => 'Health and beauty products'],
                ['name' => 'Sports & Outdoors', 'description' => 'Sports equipment and outdoor gear'],
            ];

            foreach ($categories as $category) {
                Category::create(array_merge($category, [
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
            
            $this->command->info('Seeded basic categories');
        }
    }

    /**
     * Seed sample products if table is empty
     */
    private function seedSampleProducts(): void
    {
        if (Product::count() == 0) {
            $categories = Category::all();
            
            if ($categories->count() > 0) {
                $products = [
                    [
                        'name' => 'Sample Product 1',
                        'description' => 'This is a sample product for testing',
                        'price' => 99.99,
                        'category_id' => $categories->first()->id,
                        'active' => true,
                        'stock_quantity' => 100,
                    ],
                    [
                        'name' => 'Sample Product 2', 
                        'description' => 'Another sample product for testing',
                        'price' => 149.99,
                        'category_id' => $categories->first()->id,
                        'active' => true,
                        'stock_quantity' => 50,
                    ]
                ];

                foreach ($products as $product) {
                    Product::create(array_merge($product, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
                
                $this->command->info('Seeded sample products');
            }
        }
    }
}