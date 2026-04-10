<?php

namespace Tests\Unit\Services;

use App\Http\Services\NotificationService;
use App\Repositories\Eloquent\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_delete_should_call_repository_delete_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('delete')->once()->with(10);

        // Act
        $service->delete(10);

        // Assert
        $this->assertTrue(true);
    }

    public function test_delete_should_call_repository_with_edge_id_zero(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('delete')->once()->with(0);

        // Act
        $service->delete(0);

        // Assert
        $this->assertTrue(true);
    }

    public function test_delete_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('delete')
            ->once()
            ->with(10)
            ->andThrow(new \RuntimeException('delete failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('delete failed');

        // Act
        $service->delete(10);
    }

    public function test_list_should_return_user_notifications_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $notificationRepo->shouldReceive('getUserNotifications')
            ->once()
            ->with(1, 10, 1, false)
            ->andReturn($paginator);

        // Act
        $result = $service->list(1);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_list_should_handle_edge_values_for_unread_only(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $notificationRepo->shouldReceive('getUserNotifications')
            ->once()
            ->with(1, 1, 1, true)
            ->andReturn($paginator);

        // Act
        $result = $service->list(1, 1, 1, true);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_list_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('getUserNotifications')
            ->once()
            ->with(1, 10, 1, false)
            ->andThrow(new \RuntimeException('list failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('list failed');

        // Act
        $service->list(1);
    }

    public function test_get_unread_count_should_return_count_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('getUnreadCount')->once()->with(2)->andReturn(5);

        // Act
        $result = $service->getUnreadCount(2);

        // Assert
        $this->assertSame(5, $result);
    }

    public function test_get_unread_count_should_return_zero_for_edge_case(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('getUnreadCount')->once()->with(2)->andReturn(0);

        // Act
        $result = $service->getUnreadCount(2);

        // Assert
        $this->assertSame(0, $result);
    }

    public function test_get_unread_count_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('getUnreadCount')
            ->once()
            ->with(2)
            ->andThrow(new \RuntimeException('count failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('count failed');

        // Act
        $service->getUnreadCount(2);
    }

    public function test_mark_as_read_should_call_repository_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsRead')->once()->with(7);

        // Act
        $service->markAsRead(7);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_as_read_should_call_repository_with_edge_id_zero(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsRead')->once()->with(0);

        // Act
        $service->markAsRead(0);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_as_read_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsRead')
            ->once()
            ->with(7)
            ->andThrow(new \RuntimeException('mark read failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('mark read failed');

        // Act
        $service->markAsRead(7);
    }

    public function test_mark_as_unread_should_call_repository_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsUnread')->once()->with(8);

        // Act
        $service->markAsUnread(8);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_as_unread_should_call_repository_with_edge_id_zero(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsUnread')->once()->with(0);

        // Act
        $service->markAsUnread(0);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_as_unread_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAsUnread')
            ->once()
            ->with(8)
            ->andThrow(new \RuntimeException('mark unread failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('mark unread failed');

        // Act
        $service->markAsUnread(8);
    }

    public function test_mark_all_as_read_should_call_repository_successfully(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAllAsRead')->once()->with(3);

        // Act
        $service->markAllAsRead(3);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_all_as_read_should_call_repository_with_edge_user_id_zero(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAllAsRead')->once()->with(0);

        // Act
        $service->markAllAsRead(0);

        // Assert
        $this->assertTrue(true);
    }

    public function test_mark_all_as_read_should_throw_when_repository_throws(): void
    {
        // Arrange
        $notificationRepo = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($notificationRepo);

        $notificationRepo->shouldReceive('markAllAsRead')
            ->once()
            ->with(3)
            ->andThrow(new \RuntimeException('mark all failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('mark all failed');

        // Act
        $service->markAllAsRead(3);
    }
}
