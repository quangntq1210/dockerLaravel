<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Quản lý thông báo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand fw-bold">Quản lý thông báo</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white">{{ auth()->user()->name }}</span>
      <form action="/logout" method="POST">
        @csrf
        <button class="btn btn-outline-light btn-sm">Đăng xuất</button>
      </form>
    </div>
  </nav>

  <div class="container py-4">
    @yield('content')
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/helpers/formatTimeAgo.js') }}"></script>
  <script src="{{ asset('js/helpers/showToast.js') }}"></script>
  <script src="{{ asset('js/helpers/renderPagination.js') }}"></script>
  @yield('scripts')
</body>

</html>