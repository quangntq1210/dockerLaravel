<?php

namespace Tests\Unit\Services;

use App\Http\Services\CampaignRecipientService;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CampaignRecipientServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_campaign_recipients_bulk_should_use_existing_subscriber_successfully(): void
    {
        // Arrange
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $service = new CampaignRecipientService($campaignRecipientsRepo, $subscriberRepo);

        $subscriber = (object) ['id' => 15];
        $campaigns = [101, 102];

        $subscriberRepo->shouldReceive('getByUserId')
            ->once()
            ->with(1)
            ->andReturn($subscriber);

        $subscriberRepo->shouldNotReceive('create');

        $campaignRecipientsRepo->shouldReceive('createCampaignRecipientsBulk')
            ->once()
            ->with(15, $campaigns);

        // Act
        $service->createCampaignRecipientsBulk(1, $campaigns);

        // Assert
        $this->assertTrue(true);
    }

    public function test_create_campaign_recipients_bulk_should_create_subscriber_when_missing_edge_case(): void
    {
        // Arrange
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $service = new CampaignRecipientService($campaignRecipientsRepo, $subscriberRepo);

        $campaigns = [];
        $fakeUser = (object) [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];
        $createdSubscriber = (object) ['id' => 27];

        $subscriberRepo->shouldReceive('getByUserId')
            ->once()
            ->with(2)
            ->andReturn(null);

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($fakeUser);

        $subscriberRepo->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'user_id' => 2,
            ])
            ->andReturn($createdSubscriber);

        $campaignRecipientsRepo->shouldReceive('createCampaignRecipientsBulk')
            ->once()
            ->with(27, $campaigns);

        // Act
        $service->createCampaignRecipientsBulk(2, $campaigns);

        // Assert
        $this->assertTrue(true);
    }

    public function test_create_campaign_recipients_bulk_should_throw_when_repository_throws(): void
    {
        // Arrange
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepositoryInterface::class);
        $subscriberRepo = Mockery::mock(SubscriberRepositoryInterface::class);
        $service = new CampaignRecipientService($campaignRecipientsRepo, $subscriberRepo);

        $campaigns = [201];

        $subscriberRepo->shouldReceive('getByUserId')
            ->once()
            ->with(3)
            ->andThrow(new \RuntimeException('subscriber lookup failed'));

        $campaignRecipientsRepo->shouldNotReceive('createCampaignRecipientsBulk');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('subscriber lookup failed');

        // Act
        $service->createCampaignRecipientsBulk(3, $campaigns);
    }
}
