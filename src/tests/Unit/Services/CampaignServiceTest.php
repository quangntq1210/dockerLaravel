<?php

namespace Tests\Unit\Services;

use App\Http\Services\CampaignService;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CampaignServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_draft_by_subscriber_id_should_return_drafts_for_current_subscriber_successfully(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $service = new CampaignService($campaignRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $user = (object) [
            'subscriber' => (object) ['id' => 15],
        ];

        Auth::shouldReceive('user')->once()->andReturn($user);

        $campaignRepo->shouldReceive('getDraftBySubscriberId')
            ->once()
            ->with(15, 20, 1)
            ->andReturn($paginator);

        // Act
        $result = $service->getDraftBySubscriberId(20, 1);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_draft_by_subscriber_id_should_use_null_subscriber_id_when_user_has_no_subscriber_edge_case(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $service = new CampaignService($campaignRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $user = (object) [
            'subscriber' => null,
        ];

        Auth::shouldReceive('user')->once()->andReturn($user);

        $campaignRepo->shouldReceive('getDraftBySubscriberId')
            ->once()
            ->with(null, 1, 1)
            ->andReturn($paginator);

        // Act
        $result = $service->getDraftBySubscriberId(1, 1);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_draft_by_subscriber_id_should_throw_when_repository_throws(): void
    {
        // Arrange
        $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
        $service = new CampaignService($campaignRepo);

        $user = (object) [
            'subscriber' => (object) ['id' => 3],
        ];

        Auth::shouldReceive('user')->once()->andReturn($user);

        $campaignRepo->shouldReceive('getDraftBySubscriberId')
            ->once()
            ->with(3, 20, 1)
            ->andThrow(new \RuntimeException('get drafts failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('get drafts failed');

        // Act
        $service->getDraftBySubscriberId(20, 1);
    }
}
