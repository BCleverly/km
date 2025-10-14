<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Tasks\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAffiliateLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_have_multiple_affiliate_links(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $affiliateLinks = AffiliateLink::factory()->count(3)->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach($affiliateLinks->pluck('id')->toArray());

        $this->assertCount(3, $task->affiliateLinks);
    }

    public function test_affiliate_link_can_be_shared_between_tasks(): void
    {
        $user = User::factory()->create();
        
        $task1 = Task::factory()->create(['user_id' => $user->id]);
        $task2 = Task::factory()->create(['user_id' => $user->id]);
        
        $affiliateLink = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        $task1->affiliateLinks()->attach($affiliateLink->id);
        $task2->affiliateLinks()->attach($affiliateLink->id);

        $this->assertCount(1, $task1->affiliateLinks);
        $this->assertCount(1, $task2->affiliateLinks);
        $this->assertEquals($affiliateLink->id, $task1->affiliateLinks->first()->id);
        $this->assertEquals($affiliateLink->id, $task2->affiliateLinks->first()->id);
    }

    public function test_affiliate_links_are_ordered_by_sort_order(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $link1 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $link2 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $link3 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach([
            $link1->id => ['sort_order' => 3],
            $link2->id => ['sort_order' => 1],
            $link3->id => ['sort_order' => 2],
        ]);

        $orderedLinks = $task->affiliateLinks;
        
        $this->assertEquals($link2->id, $orderedLinks[0]->id);
        $this->assertEquals($link3->id, $orderedLinks[1]->id);
        $this->assertEquals($link1->id, $orderedLinks[2]->id);
    }

    public function test_can_set_primary_affiliate_link(): void
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
        $this->assertTrue($primaryAffiliateLink->pivot->is_primary);
    }

    public function test_can_detach_affiliate_links_from_task(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $affiliateLinks = AffiliateLink::factory()->count(2)->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach($affiliateLinks->pluck('id')->toArray());
        
        $this->assertCount(2, $task->affiliateLinks);
        
        $task->affiliateLinks()->detach($affiliateLinks->first()->id);
        
        $this->assertCount(1, $task->fresh()->affiliateLinks);
    }

    public function test_can_sync_affiliate_links(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $link1 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $link2 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        $link3 = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        // Attach first two links
        $task->affiliateLinks()->attach([
            $link1->id => ['sort_order' => 1],
            $link2->id => ['sort_order' => 2],
        ]);
        
        $this->assertCount(2, $task->affiliateLinks);
        
        // Sync with different links
        $task->affiliateLinks()->sync([
            $link2->id => ['sort_order' => 1],
            $link3->id => ['sort_order' => 2],
        ]);
        
        $this->assertCount(2, $task->fresh()->affiliateLinks);
        $this->assertTrue($task->fresh()->affiliateLinks->contains($link2));
        $this->assertTrue($task->fresh()->affiliateLinks->contains($link3));
        $this->assertFalse($task->fresh()->affiliateLinks->contains($link1));
    }

    public function test_pivot_data_is_preserved(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $affiliateLink = AffiliateLink::factory()->create(['user_id' => $user->id]);
        
        $task->affiliateLinks()->attach($affiliateLink->id, [
            'link_text' => 'Buy this amazing product',
            'description' => 'Perfect for completing this task',
            'sort_order' => 5,
            'is_primary' => true,
        ]);

        $pivot = $task->affiliateLinks->first()->pivot;
        
        $this->assertEquals('Buy this amazing product', $pivot->link_text);
        $this->assertEquals('Perfect for completing this task', $pivot->description);
        $this->assertEquals(5, $pivot->sort_order);
        $this->assertTrue($pivot->is_primary);
    }
}