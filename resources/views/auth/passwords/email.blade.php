<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f8fafc">
<div class="card p-4" style="width:400px;border-radius:12px">
    <h5 class="mb-3">Reset your password</h5>
    <p class="text-muted small">Enter your email and we'll send a link to reset your password.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required value="{{ old('email') }}">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}">Back to login</a>
            <button class="btn btn-primary">Send reset link</button>
        </div>
    </form>
</div>
</body>
</html>
