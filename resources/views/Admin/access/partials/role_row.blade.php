<tr class="role-row" data-role-id="{{ $role->id }}" data-parent-id="{{ $role->parent_id }}">
    <td style="padding-left: {{ $level * 20 }}px;">
        @if($role->children->isNotEmpty())
            <a href="#" class="toggle-role"><i class="fas fa-chevron-right me-2"></i></a>
        @endif
        {{ $role->name }}
    </td>
    <td>
        <a href="#" class="btn btn-sm btn-outline-secondary border-0 edit-role-btn"
           data-bs-toggle="modal" data-bs-target="#editRoleModal"
           data-role-id="{{ $role->id }}"
           data-role-name="{{ $role->name }}"
           data-role-description="{{ $role->description ?? '' }}">
            <i class="fas fa-pencil-alt"></i>
        </a>
        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
@if($role->children->isNotEmpty())
    @foreach($role->children as $child)
        @include('admin.access.partials.role_row', ['role' => $child, 'level' => $level + 1])
    @endforeach
@endif
