<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Administrator\StoreAdministratorRequest;
use App\Http\Requests\Admin\Administrator\UpdateAdministratorRequest;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdministratorController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.administrators.index', [
            'tableId' => 'administrators-bootstrap-table',
            'dataUrl' => route('admin.api.administrators.table'),
            'currentAdminId' => (int) Auth::guard('admin')->id(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.administrators.create');
    }

    public function store(StoreAdministratorRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'password', 'is_active']);
        Administrator::query()->create($data);

        return redirect()
            ->route('admin.administrators.index')
            ->with('success', __('Administrator created.'));
    }

    public function edit(Administrator $administrator): View
    {
        return view('admin.pages.administrators.edit', [
            'administrator' => $administrator,
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
}
