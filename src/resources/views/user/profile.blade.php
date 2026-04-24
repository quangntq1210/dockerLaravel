@extends('user.layouts.user')

@section('content')
    <div class="profile-page-wrap">
        <div class="profile-card">
            @if (session('success'))
                <div class="alert alert-success profile-alert mb-0" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="profile-card-header">
                @if (!empty($user->avatar_display_url))
                    <img src="{{ $user->avatar_display_url }}" alt="Avatar" class="profile-avatar-image">
                @else
                    <span class="profile-avatar">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </span>
                @endif
                <div>
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <p class="profile-email">{{ $user->email }}</p>
                </div>
            </div>

            <div class="profile-card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                    class="profile-form">
                    @csrf
                    @method('PUT')
                    <h2 class="profile-section-title">{{ __('message.profile_information') }}</h2>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('message.name') }}</label>
                        <input type="text" id="name" name="name" class="form-control"
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">{{ __('message.avatar') }}</label>
                        <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                        @error('avatar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('message.update_profile') }}</button>
                </form>

                <hr class="my-4">

                <form action="{{ route('profile.password.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')
                    <h2 class="profile-section-title">{{ __('message.change_password') }}</h2>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">{{ __('message.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password" class="form-control">
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('message.new_password') }}</label>
                        <input type="password" id="password" name="password" class="form-control">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation"
                            class="form-label">{{ __('message.confirm_new_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-outline-primary">{{ __('message.update_password') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
