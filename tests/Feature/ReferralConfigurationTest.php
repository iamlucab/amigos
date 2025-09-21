<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ReferralConfiguration;

class ReferralConfigurationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a referral configuration can be created.
     *
     * @return void
     */
    public function test_can_create_referral_configuration()
    {
        $config = ReferralConfiguration::create([
            'name' => 'Test Configuration',
            'total_allocation' => 1000,
            'max_level' => 5,
            'level_bonuses' => [
                '1' => 200,
                '2' => 150,
                '3' => 100,
            ],
            'description' => 'Test configuration',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('referral_configurations', [
            'name' => 'Test Configuration',
            'total_allocation' => 1000,
            'max_level' => 5,
            'is_active' => 1,
        ]);

        $this->assertEquals('Test Configuration', $config->name);
        $this->assertEquals(1000, $config->total_allocation);
        $this->assertEquals(5, $config->max_level);
        $this->assertEquals([
            '1' => 200,
            '2' => 150,
            '3' => 100,
        ], $config->level_bonuses);
    }

    /**
     * Test that getActive returns the active configuration.
     *
     * @return void
     */
    public function test_get_active_returns_active_configuration()
    {
        // Create an inactive configuration
        ReferralConfiguration::create([
            'name' => 'Inactive Configuration',
            'total_allocation' => 500,
            'max_level' => 3,
            'is_active' => false,
        ]);

        // Create an active configuration
        ReferralConfiguration::create([
            'name' => 'Active Configuration',
            'total_allocation' => 1000,
            'max_level' => 5,
            'is_active' => true,
        ]);

        $activeConfig = ReferralConfiguration::getActive();

        $this->assertNotNull($activeConfig);
        $this->assertEquals('Active Configuration', $activeConfig->name);
    }

    /**
     * Test that bonuses are distributed equally when no custom levels are defined.
     *
     * @return void
     */
    public function test_equal_distribution_when_no_custom_levels()
    {
        $config = ReferralConfiguration::create([
            'name' => 'Equal Distribution',
            'total_allocation' => 1000,
            'max_level' => 5,
            'level_bonuses' => [],
            'is_active' => true,
        ]);

        $bonuses = $config->getAllBonuses();

        $this->assertEquals(5, count($bonuses));
        $this->assertEquals(200, $bonuses[1]);
        $this->assertEquals(200, $bonuses[2]);
        $this->assertEquals(200, $bonuses[3]);
        $this->assertEquals(200, $bonuses[4]);
        $this->assertEquals(200, $bonuses[5]);
    }

    /**
     * Test that custom levels work with auto-distribution for remaining levels.
     *
     * @return void
     */
    public function test_custom_levels_with_auto_distribution()
    {
        $config = ReferralConfiguration::create([
            'name' => 'Custom with Auto',
            'total_allocation' => 1000,
            'max_level' => 5,
            'level_bonuses' => [
                '1' => 300,
                '2' => 200,
            ],
            'is_active' => true,
        ]);

        $bonuses = $config->getAllBonuses();

        $this->assertEquals(5, count($bonuses));
        $this->assertEquals(300, $bonuses[1]);
        $this->assertEquals(200, $bonuses[2]);
        
        // Remaining 500 should be distributed equally among 3 levels
        $this->assertEquals(round(500/3, 2), $bonuses[3]);
        $this->assertEquals(round(500/3, 2), $bonuses[4]);
        $this->assertEquals(round(500/3, 2), $bonuses[5]);
    }

    /**
     * Test that fully custom levels work correctly.
     *
     * @return void
     */
    public function test_fully_custom_levels()
    {
        $config = ReferralConfiguration::create([
            'name' => 'Fully Custom',
            'total_allocation' => 1000,
            'max_level' => 5,
            'level_bonuses' => [
                '1' => 300,
                '2' => 250,
                '3' => 200,
                '4' => 150,
                '5' => 100,
            ],
            'is_active' => true,
        ]);

        $bonuses = $config->getAllBonuses();

        $this->assertEquals(5, count($bonuses));
        $this->assertEquals(300, $bonuses[1]);
        $this->assertEquals(250, $bonuses[2]);
        $this->assertEquals(200, $bonuses[3]);
        $this->assertEquals(150, $bonuses[4]);
        $this->assertEquals(100, $bonuses[5]);
    }
}
