<?php

namespace Tests\Unit\Services;

use App\Http\Services\CampaignSchedulingService;
use App\Repositories\Eloquent\CampaignRecipientsRepository;
use App\Repositories\Eloquent\CampaignRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class CampaignSchedulingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_draft_and_created_at_descending_should_return_collection_successfully(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $expected = collect([
            ['id' => 1, 'status' => 'draft'],
            ['id' => 2, 'status' => 'draft'],
        ]);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->andReturn($expected);

        // Act
        $result = $service->getDraftAndCreatedAtDescending();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result);
    }

    public function test_get_draft_and_created_at_descending_should_return_empty_collection_edge_case(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->andReturn(collect());

        // Act
        $result = $service->getDraftAndCreatedAtDescending();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_get_draft_and_created_at_descending_should_throw_when_repository_throws(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $campaignRepo->shouldReceive('getDraftAndCreatedAtDescending')
            ->once()
            ->andThrow(new \RuntimeException('get drafts failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('get drafts failed');

        // Act
        $service->getDraftAndCreatedAtDescending();
    }

    public function test_create_campaign_scheduling_should_update_campaign_and_create_recipients_successfully(): void
    {
        // Arrange
        Carbon::setTestNow('2026-01-01 00:00:00');

        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $data = [
            'campaign_id' => 99,
            'send_at' => '2026-01-10 09:00:00',
            'subscriber_ids' => [11, 12],
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        DB::shouldReceive('rollBack')->never();
        Log::shouldReceive('error')->never();

        $campaignRepo->shouldReceive('update')
            ->once()
            ->with(
                [
                    'send_at' => $data['send_at'],
                    'status' => 'scheduled',
                ],
                99
            );

        $campaignRecipientsRepo->shouldReceive('deleteByCampaignId')
            ->once()
            ->with(99);

        $campaignRecipientsRepo->shouldReceive('createBulk')
            ->once()
            ->with([
                [
                    'campaign_id' => 99,
                    'subscriber_id' => 11,
                    'status' => 'pending',
                    'sent_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'campaign_id' => 99,
                    'subscriber_id' => 12,
                    'status' => 'pending',
                    'sent_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

        // Act
        $service->createCampaignScheduling($data);

        // Assert
        $this->assertTrue(true);
    }

    public function test_create_campaign_scheduling_should_handle_empty_subscriber_ids_edge_case(): void
    {
        // Arrange
        Carbon::setTestNow('2026-01-01 00:00:00');

        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $data = [
            'campaign_id' => 7,
            'send_at' => '2026-01-12 10:00:00',
            'subscriber_ids' => [],
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        DB::shouldReceive('rollBack')->never();
        Log::shouldReceive('error')->never();

        $campaignRepo->shouldReceive('update')
            ->once()
            ->with(
                [
                    'send_at' => $data['send_at'],
                    'status' => 'scheduled',
                ],
                7
            );

        $campaignRecipientsRepo->shouldReceive('deleteByCampaignId')
            ->once()
            ->with(7);

        $campaignRecipientsRepo->shouldReceive('createBulk')
            ->once()
            ->with([]);

        // Act
        $service->createCampaignScheduling($data);

        // Assert
        $this->assertTrue(true);
    }

    public function test_create_campaign_scheduling_should_log_and_rethrow_when_dependency_throws(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepository::class);
        $campaignRecipientsRepo = Mockery::mock(CampaignRecipientsRepository::class);
        $service = new CampaignSchedulingService($campaignRepo, $campaignRecipientsRepo);

        $data = [
            'campaign_id' => 12,
            'send_at' => '2026-01-15 08:30:00',
            'subscriber_ids' => [5],
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        DB::shouldReceive('rollBack')->once();
        Log::shouldReceive('error')->once()->with('update failed');

        $campaignRepo->shouldReceive('update')
            ->once()
            ->andThrow(new \RuntimeException('update failed'));

        $campaignRecipientsRepo->shouldNotReceive('deleteByCampaignId');
        $campaignRecipientsRepo->shouldNotReceive('createBulk');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('update failed');

        // Act
        $service->createCampaignScheduling($data);
    }
}
