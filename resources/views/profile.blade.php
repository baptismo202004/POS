<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { font-family: Inter, sans-serif; background: #ffffff; }
        .sidebar { width: 220px; }
        .avatar-xl { width:110px;height:110px;border-radius:14px;object-fit:cover; }
        .card-soft { background:#f8fafc;border-radius:12px;padding:16px }
        .activity { border-bottom:1px solid #eef2f7;padding:10px 0 }
          /* Avatar with camera */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .camera-btn {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 36px;
            height: 36px;
            background: #ffffff;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .camera-btn i {
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="d-flex min-vh-100">

    @include('layouts.AdminSidebar')

    <main class="flex-fill p-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card p-3 card-soft">
                        
                        <div class="text-center">
                            @php $user = auth()->user(); @endphp
                            @if(!empty($avatarUrl))
                                <div class="avatar-wrapper mb-3">
                                    <img src="{{ $avatarUrl }}" alt="avatar" class="avatar-xl">
                                    <label for="avatarInput" class="camera-btn" title="Change picture"><i class="bi bi-camera"></i></label>
                                </div>
                            @else
                                <div class="avatar-wrapper mb-3">
                                    <div class="avatar-xl d-inline-flex align-items-center justify-content-center bg-white text-dark border border-secondary"></div>
                                    <label for="avatarInput" class="camera-btn" title="Change picture"><i class="bi bi-camera"></i></label>
                                </div>
                            @endif
                            <div class="small text-muted mb-3">
                                <label for="avatarInput" style="cursor:pointer">Change Profile Picture</label>
                            </div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        <hr>
                        <form id="avatarForm" action="{{ url('/profile/avatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                  <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Upload</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card p-3 mb-4">
                        <h5>Change Password</h5>
                        <form action="{{ url('/profile/password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button class="btn btn-success" type="submit">Change Password</button>
                        </form>
                    </div>

                    <div class="card p-3">
                        <h5 class="mb-3">Recent Activities</h5>
                        @if(isset($activities) && count($activities))
                            <div style="max-height:320px;overflow:auto">
                                @foreach($activities as $act)
                                    <div class="activity">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="fw-semibold">{{ $act->title ?? $act['title'] ?? 'Activity' }}</div>
                                                <div class="small text-muted">{{ $act->description ?? $act['description'] ?? '' }}</div>
                                            </div>
                                            <div class="small text-muted">{{ $act->created_at ?? $act['created_at'] ?? '' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted">No recent activity.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show SweetAlert if controller set a swal session
    document.addEventListener('DOMContentLoaded', function () {
        const swalData = @json(session('swal'));
        if (swalData) {
            const options = {
                title: swalData.title || '',
                text: swalData.text || '',
                html: swalData.html || undefined,
                icon: swalData.icon || undefined,
                toast: swalData.toast || false,
                position: swalData.position || 'center',
                showConfirmButton: swalData.showConfirmButton !== undefined ? swalData.showConfirmButton : true,
                timer: swalData.timer || undefined,
            };
            Swal.fire(options);
        }

        // If there are validation errors (Laravel), show a SweetAlert as well
        @if ($errors->any())
            const errs = `{!! implode('<br>', $errors->all()) !!}`;
            Swal.fire({
                title: 'Please fix the errors',
                html: errs,
                icon: 'error',
                toast: true,
                position: 'top-end',
                timer: 4500,
                showConfirmButton: false
            });
        @endif
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatarInput');
    if (!input) return;

    input.addEventListener('change', function (e) {
        const file = this.files && this.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (ev) {
            const wrapper = document.querySelector('.avatar-wrapper');
            if (!wrapper) return;

            let img = wrapper.querySelector('img.avatar-xl');
            if (!img) {
                img = document.createElement('img');
                img.className = 'avatar-xl';
                // remove any placeholder inside wrapper
                const placeholder = wrapper.querySelector('div.avatar-xl');
                if (placeholder) placeholder.remove();
                // insert image before camera button
                const camera = wrapper.querySelector('.camera-btn');
                if (camera) wrapper.insertBefore(img, camera);
                else wrapper.appendChild(img);
            }
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
        // auto-submit the avatar form so upload happens immediately
        const form = document.getElementById('avatarForm');
        if (form) {
            // small delay to allow preview to render
            setTimeout(() => form.submit(), 250);
        }
    });
});
</script>
</body>
</html>
