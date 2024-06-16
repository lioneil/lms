<?php

namespace Subscription\Unit\Events;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Subscription\Events\UserSubscribed;
use Subscription\Events\UserUnsubscribed;
use Subscription\Models\Subscription;
use Tests\TestCase;

/**
 * @package Subscription\Unit\Events
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SubcriptionEventsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  service:course
     * @return void
     */
    public function it_should_fire_a_subscribed_event_when_a_subscription_is_created()
    {
        // Arrangements
        Event::fake();

        // Actions
        $subscription = factory(Subscription::class, 2)->create()->random();
        $subscription->save();

        // Assertions
        Event::assertDispatched(UserSubscribed::class, function ($e) use ($subscription) {
            return $e->subscription->id == $subscription->id;
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  service:course
     * @return void
     */
    public function it_should_fire_an_unsubscribed_event_when_a_subscription_is_deleted()
    {
        // Arrangements
        Event::fake();

        // Actions
        $subscription = factory(Subscription::class, 2)->create()->random();
        $subscription->delete();

        // Assertions
        Event::assertDispatched(UserUnsubscribed::class, function ($e) use ($subscription) {
            return $e->subscription->id == $subscription->id;
        });
    }
}
