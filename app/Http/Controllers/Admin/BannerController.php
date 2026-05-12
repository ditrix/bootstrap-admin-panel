<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\Admin\BannerAttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Banners catalogue (bootstrap-table index, attachment upload/remove, delete with child guard).
 */
class BannerController extends Controller
{
    public function __construct(
        private BannerAttachmentService $bannerAttachmentService,
    ) {}

    public function index(): View
    {
        return view('admin.pages.banners.view', [
            'tableId' => 'banners-bootstrap-table',
            'dataUrl' => route('admin.api.banners.table'),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.banners.create', [
            'parents' => Banner::query()->ordered()->get(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $banner = Banner::query()->create($request->payloadForModel());
        if ($request->hasFile('banner_image')) {
            $this->bannerAttachmentService->storeUploadedImage($banner, $request->file('banner_image'));
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', __('Banner created.'));
    }

    public function edit(Banner $banner): View
    {
        return view('admin.pages.banners.edit', [
            'banner' => $banner,
            'parents' => Banner::query()
                ->whereKeyNot($banner->getKey())
                ->ordered()
                ->get(),
        ]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $banner->update($request->payloadForModel());
        if ($request->hasFile('banner_image')) {
            $this->bannerAttachmentService->storeUploadedImage($banner, $request->file('banner_image'));
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', __('Banner updated.'));
    }

    public function destroy(Request $request, Banner $banner): JsonResponse|RedirectResponse
    {
        if (Banner::query()->where('parent_id', $banner->getKey())->exists()) {
            $message = __('Cannot delete a banner that has child banners.');
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()
                ->route('admin.banners.index')
                ->with('error', $message);
        }

        $this->bannerAttachmentService->clearStoredImage($banner);
        $banner->delete();
        $message = __('Banner deleted.');
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', $message);
    }

    public function destroyImage(Banner $banner): JsonResponse
    {
        $this->bannerAttachmentService->clearStoredImage($banner);

        return response()->json(['message' => __('Banner image removed.')]);
    }
}
