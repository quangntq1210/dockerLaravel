<?php

namespace Tests\Unit\Services;

use App\Http\Services\AdminService;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class AdminServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_dashboard_data_should_return_cached_value_when_cache_hit(): void
    {
        // Arrange
        $dashboardRepo = Mockery::mock(DashboardRepositoryInterface::class);
        $service = new AdminService($dashboardRepo);

        $request = (object) [
            'q' => 'abc',
            'campaign_id' => 10,
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ];

        $expected = [
            'stats' => ['total' => 1],
            'data' => [['id' => 1]],
        ];

        $dashboardRepo->shouldNotReceive('getStats');
        $dashboardRepo->shouldNotReceive('getCampaignReport');

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), 60, Mockery::type('callable'))
            ->andReturn($expected);

        // Act
        $result = $service->getDashboardData($request);

        // Assert
        $this->assertSame($expected, $result);
    }

    public function test_get_dashboard_data_should_compute_and_cache_value_when_cache_miss(): void
    {
        // Arrange
        $dashboardRepo = Mockery::mock(DashboardRepositoryInterface::class);
        $service = new AdminService($dashboardRepo);

        $request = (object) [
            'q' => null,
            'campaign_id' => null,
            'from' => null,
            'to' => null,
        ];

        $stats = ['total_campaigns' => 3];
        $report = [['campaign_id' => 1, 'count' => 2]];

        $dashboardRepo->shouldReceive('getStats')->once()->andReturn($stats);
        $dashboardRepo->shouldReceive('getCampaignReport')
            ->once()
            ->with([
                'q' => null,
                'campaign_id' => null,
                'from' => null,
                'to' => null,
            ])
            ->andReturn($report);

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), 60, Mockery::type('callable'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        // Act
        $result = $service->getDashboardData($request);

        // Assert
        $this->assertSame(
            [
                'stats' => $stats,
                'data' => $report,
            ],
            $result
        );
    }

    public function test_get_dashboard_data_should_throw_when_repository_throws(): void
    {
        // Arrange
        $dashboardRepo = Mockery::mock(DashboardRepositoryInterface::class);
        $service = new AdminService($dashboardRepo);

        $request = (object) [
            'q' => 'x',
            'campaign_id' => 1,
            'from' => '2026-01-01',
            'to' => '2026-01-02',
        ];

        $dashboardRepo->shouldReceive('getStats')->once()->andThrow(new \RuntimeException('boom'));
        $dashboardRepo->shouldNotReceive('getCampaignReport');

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), 60, Mockery::type('callable'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');

        // Act
        $service->getDashboardData($request);
    }
}

