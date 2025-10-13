<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Tasks\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_affiliate_link(): void
    {
        $user = User::factory()->create();
        
        $affiliateLink = AffiliateLink::create([
            'name' => 'Test Partner',
            'description' => 'Test partner description',
            'url' => 'https://example.com/affiliate',
            'partner_type' => 'toys',
            'commission_type' => 'percentage',
            'commission_rate' => 5.0,
            'currency' => 'USD',
            'is_active' => true,
            'is_premium' => false,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('affiliate_links', [
            'name' => 'Test Partner',
            'url' => 'https://example.com/affiliate',
            'partner_type' => 'toys',
        ]);
    }

    public function test_can_attach_affiliate_links_to_task(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $affiliateLink1 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $affiliateLink2 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach([
            $affiliateLink1->id => [
                'link_text' => 'Buy this toy',
                'description' => 'Perfect for this task',
                'sort_order' => 1,
                'is_primary' => true,
            ],
            $affiliateLink2->id => [
                'link_text' => 'Alternative option',
                'description' => 'Another great choice',
                'sort_order' => 2,
                'is_primary' => false,
            ],
        ]);

        $this->assertCount(2, $task->affiliateLinks);
        $this->assertTrue($task->affiliateLinks->contains($affiliateLink1));
        $this->assertTrue($task->affiliateLinks->contains($affiliateLink2));
    }

    public function test_can_get_primary_affiliate_link(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $primaryLink = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $secondaryLink = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach([
            $primaryLink->id => ['is_primary' => true, 'sort_order' => 1],
            $secondaryLink->id => ['is_primary' => false, 'sort_order' => 2],
        ]);

        $primaryAffiliateLink = $task->primaryAffiliateLink()->first();
        
        $this->assertEquals($primaryLink->id, $primaryAffiliateLink->id);
    }

    public function test_can_calculate_commission(): void
    {
        $user = User::factory()->create();
        
        $percentageLink = AffiliateLink::factory()->create([
            'user_id' => $user->id,
            'commission_type' => 'percentage',
            'commission_rate' => 10.0,
        ]);
        
        $fixedLink = AffiliateLink::factory()->create([
            'user_id' => $user->id,
            'commission_type' => 'fixed',
            'commission_fixed' => 5.0,
        ]);

        $this->assertEquals(10.0, $percentageLink->calculateCommission(100.0));
        $this->assertEquals(5.0, $fixedLink->calculateCommission(100.0));
    }

    public function test_can_scope_active_affiliate_links(): void
    {
        $user = User::factory()->create();
        
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_active' => false]);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $activeLinks = AffiliateLink::active()->get();
        
        $this->assertCount(2, $activeLinks);
        $this->assertTrue($activeLinks->every(fn($link) => $link->is_active));
    }

    public function test_can_scope_premium_affiliate_links(): void
    {
        $user = User::factory()->create();
        
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_premium' => true]);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_premium' => false]);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'is_premium' => true]);

        $premiumLinks = AffiliateLink::premium()->get();
        
        $this->assertCount(2, $premiumLinks);
        $this->assertTrue($premiumLinks->every(fn($link) => $link->is_premium));
    }

    public function test_can_scope_by_partner_type(): void
    {
        $user = User::factory()->create();
        
        AffiliateLink::factory()->create(['user_id' => $user->id, 'partner_type' => 'toys']);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'partner_type' => 'clothing']);
        AffiliateLink::factory()->create(['user_id' => $user->id, 'partner_type' => 'toys']);

        $toyLinks = AffiliateLink::ofType('toys')->get();
        
        $this->assertCount(2, $toyLinks);
        $this->assertTrue($toyLinks->every(fn($link) => $link->partner_type === 'toys'));
    }

    public function test_affiliate_link_has_formatted_commission_rate(): void
    {
        $user = User::factory()->create();
        
        $percentageLink = AffiliateLink::factory()->create([
            'user_id' => $user->id,
            'commission_type' => 'percentage',
            'commission_rate' => 8.5,
        ]);
        
        $fixedLink = AffiliateLink::factory()->create([
            'user_id' => $user->id,
            'commission_type' => 'fixed',
            'commission_fixed' => 10.0,
            'currency' => 'USD',
        ]);

        $this->assertEquals('8.50%', $percentageLink->formatted_commission_rate);
        $this->assertEquals('USD 10.00', $fixedLink->formatted_commission_rate);
    }
}