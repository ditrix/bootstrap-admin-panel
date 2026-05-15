<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Administrator\StoreAdministratorRequest;
use App\Http\Requests\Admin\Administrator\UpdateAdministratorRequest;
use App\Models\Administrator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * CRUD for other administrator accounts (bootstrap-table, self-delete guard).
 */
class AdministratorController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.administrators.index', [
            'tableId' => 'administrators-bootstrap-table',
            'dataUrl' => route('admin.api.administrators.simple'),
            'currentAdminId' => (int) Auth::guard('admin')->id(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.administrators.create', [
            'roles' => $this->adminRoles(),
        ]);
    }

    public function store(StoreAdministratorRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'password', 'is_active', 'role_id']);
        Administrator::query()->create($data);

        return redirect()
            ->route('admin.administrators.index')
            ->with('success', __('Administrator created.'));
    }

    public function edit(Administrator $administrator): View
    {
        return view('admin.pages.administrators.edit', [
            'administrator' => $administrator,
            'roles' => $this->adminRoles(),
        ]);
    }

    public function update(UpdateAdministratorRequest $request, Administrator $administrator): RedirectResponse
    {
        $data = $request->validated();
        unset($data['password_confirmation']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $administrator->update($data);

        return redirect()
            ->route('admin.administrators.index')
            ->with('success', __('Administrator updated.'));
    }

    public function destroy(Request $request, Administrator $administrator): JsonResponse|RedirectResponse
    {
        if (Auth::guard('admin')->id() === (int) $administrator->getKey()) {
            $message = __('You cannot delete your own account.');
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()
                ->route('admin.administrators.index')
                ->with('error', $message);
        }

        $administrator->delete();
        $message = __('Administrator deleted.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.administrators.index')
            ->with('success', $message);
    }

    /**
     * @return Collection<int, Role>
     */
    private function adminRoles()
    {
        return Role::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();
    }
}
