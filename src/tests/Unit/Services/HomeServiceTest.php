<?php

namespace Tests\Unit\Services;

use App\Http\Services\HomeService;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class HomeServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_campaigns_draft_should_return_paginator_successfully(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->with(10, 1)
            ->andReturn($paginator);

        // Act
        $result = $service->getCampaignsDraft(10, 1);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_campaigns_draft_should_handle_edge_values(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->with(1, 1)
            ->andReturn($paginator);

        // Act
        $result = $service->getCampaignsDraft(1, 1);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_campaigns_draft_should_throw_when_repository_throws(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->with(10, 1)
            ->andThrow(new \RuntimeException('load drafts failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('load drafts failed');

        // Act
        $service->getCampaignsDraft(10, 1);
    }

    public function test_create_campaign_recipients_bulk_should_create_rows_and_dispatch_registered_event_successfully(): void
    {
        // Arrange
        Carbon::setTestNow('2026-01-01 00:00:00');

        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);

        $data = [
            'email' => 'user@example.com',
            'name' => 'User Test',
            'campaign_ids' => [10, 20],
        ];

        $user = Mockery::mock();
        $user->id = 5;
        $user->shouldReceive('hasVerifiedEmail')->once()->andReturn(false);

        $subscriber = (object) ['id' => 8];

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Hash::shouldReceive('make')->once()->with('12345678')->andReturn('hashed-password');
        Log::shouldReceive('error')->never();

        $userRepo->shouldReceive('firstOrCreate')
            ->once()
            ->with([
                'email' => 'user@example.com',
                'name' => 'User Test',
                'password' => 'hashed-password',
                'role' => 'user',
            ])
            ->andReturn($user);

        $subscriberRepo->shouldReceive('firstOrCreate')
            ->once()
            ->with([
                'email' => 'user@example.com',
                'name' => 'User Test',
                'user_id' => 5,
            ])
            ->andReturn($subscriber);

        $campaignRecipientsRepo->shouldReceive('existsByCampaignIdAndSubscriberId')->once()->with(10, 8)->andReturn(false);
        $campaignRecipientsRepo->shouldReceive('existsByCampaignIdAndSubscriberId')->once()->with(20, 8)->andReturn(false);

        $expectedNow = Carbon::now();
        $campaignRecipientsRepo->shouldReceive('createBulk')
            ->once()
            ->with([
                [
                    'campaign_id' => 10,
                    'subscriber_id' => 8,
                    'status' => 'draft',
                    'sent_at' => null,
                    'created_at' => $expectedNow,
                    'updated_at' => $expectedNow,
                ],
                [
                    'campaign_id' => 20,
                    'subscriber_id' => 8,
                    'status' => 'draft',
                    'sent_at' => null,
                    'created_at' => $expectedNow,
                    'updated_at' => $expectedNow,
                ],
            ]);

        $notificationRepo->shouldReceive('createBulk')
            ->once()
            ->with([
                [
                    'campaign_id' => 10,
                    'title' => 'You have been subscribed to a new campaign',
                    'message' => 'You have been subscribed to a new campaign. Please check it out.',
                    'user_id' => 5,
                    'created_at' => $expectedNow,
                    'updated_at' => $expectedNow,
                ],
                [
                    'campaign_id' => 20,
                    'title' => 'You have been subscribed to a new campaign',
                    'message' => 'You have been subscribed to a new campaign. Please check it out.',
                    'user_id' => 5,
                    'created_at' => $expectedNow,
                    'updated_at' => $expectedNow,
                ],
            ]);

        // Act
        $service->createCampaignRecipientsBulk($data);

        // Assert
        Event::assertDispatched(Registered::class);
    }

    public function test_create_campaign_recipients_bulk_should_skip_recipient_bulk_when_all_already_exist_edge_case(): void
    {
        // Arrange
        Carbon::setTestNow('2026-01-01 00:00:00');

        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);

        $data = [
            'email' => 'verified@example.com',
            'name' => 'Verified User',
            'campaign_ids' => [11],
        ];

        $user = Mockery::mock();
        $user->id = 9;
        $user->shouldReceive('hasVerifiedEmail')->once()->andReturn(true);

        $subscriber = (object) ['id' => 15];

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Hash::shouldReceive('make')->once()->with('12345678')->andReturn('hashed-password');
        Log::shouldReceive('error')->never();

        $userRepo->shouldReceive('firstOrCreate')->once()->andReturn($user);
        $subscriberRepo->shouldReceive('firstOrCreate')->once()->andReturn($subscriber);
        $campaignRecipientsRepo->shouldReceive('existsByCampaignIdAndSubscriberId')->once()->with(11, 15)->andReturn(true);
        $campaignRecipientsRepo->shouldNotReceive('createBulk');
        $notificationRepo->shouldReceive('createBulk')->once();

        // Act
        $service->createCampaignRecipientsBulk($data);

        // Assert
        Event::assertNotDispatched(Registered::class);
    }

    public function test_create_campaign_recipients_bulk_should_log_and_rethrow_when_dependency_throws(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $notificationRepo = Mockery::mock(NotificationRepositoryInterface::class);
        $service = new HomeService($campaignRepo, $subscriberRepo, $campaignRecipientsRepo, $userRepo, $notificationRepo);

        $data = [
            'email' => 'oops@example.com',
            'name' => 'Oops',
            'campaign_ids' => [1],
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Hash::shouldReceive('make')->once()->with('12345678')->andReturn('hashed-password');

        $userRepo->shouldReceive('firstOrCreate')
            ->once()
            ->andThrow(new \RuntimeException('cannot create user'));

        Log::shouldReceive('error')
            ->once()
            ->with(
                'HomeService createCampaignRecipientsBulk failed',
                Mockery::on(function ($context) use ($data) {
                    return isset($context['message'], $context['trace'], $context['data'])
                        && $context['message'] === 'cannot create user'
                        && $context['data'] === $data
                        && is_string($context['trace']);
                })
            );

        $subscriberRepo->shouldNotReceive('firstOrCreate');
        $campaignRecipientsRepo->shouldNotReceive('createBulk');
        $notificationRepo->shouldNotReceive('createBulk');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('cannot create user');

        // Act
        $service->createCampaignRecipientsBulk($data);
    }
}
