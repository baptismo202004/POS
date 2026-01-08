<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
<div class="container-fluid">
  <div class="row min-vh-100">
    <aside class="col-12 col-md-3 col-lg-2 bg-white border-end p-4">
      <div class="d-flex align-items-center mb-3">
        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px">P</div>
        <div class="ms-2 fw-bold">BGH POS</div>
        <button class="btn btn-link ms-auto d-md-none">≡</button>
      </div>
      <nav class="nav flex-column">
        <div class="nav-link text-muted small">Menu</div>
        <a class="nav-link" href="#">Products</a>
        <a class="nav-link active" href="#">Dashboard</a>
      </nav>
    </aside>
    <main class="col p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">Dashboard</h1>
          <small class="text-muted">Welcome back</small>
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="badge rounded-pill text-white" style="background:linear-gradient(135deg,#7A5CF4,#5E60CE);">Overview</div>
          <button class="btn btn-primary">New</button>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-6 col-md-3">
          <div class="card p-3">
            <div class="text-muted small">Sales</div>
            <div class="h5 fw-bold">₱ 0.00</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card p-3">
            <div class="text-muted small">Orders</div>
            <div class="h5 fw-bold">0</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card p-3">
            <div class="text-muted small">Products</div>
            <div class="h5 fw-bold">0</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card p-3">
            <div class="text-muted small">Customers</div>
            <div class="h5 fw-bold">0</div>
          </div>
        </div>
      </div>

      <div class="mt-4 card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h6 mb-0">Recent Orders</h2>
          <div class="text-muted small">Last 30 days</div>
        </div>
        <div class="table-responsive">
          <table class="table table-borderless mb-0">
            <thead class="small text-muted">
              <tr>
                <th>Customer</th><th>Items</th><th>Total</th><th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><a href="#" class="text-primary fw-semibold">No orders yet</a></td>
                <td>—</td>
                <td>—</td>
                <td class="text-end">—</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success') && request()->query('from') !== 'login')
<script>
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200, timerProgressBar: true, customClass: { popup: 'swal-toast' } });
    Toast.fire({ icon: 'success', title: {!! json_encode(session('success')) !!}, background: 'linear-gradient(90deg,#ecfdf5,#d1fae5)', color: '#065f46', iconColor: '#10b981' });
</script>
@endif
</body>
</html>
