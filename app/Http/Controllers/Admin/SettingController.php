<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function edit()
    {
        return view('admin.settings.edit', $this->settings->approvalAndGuideSettings());
    }

    public function update(UpdateSettingsRequest $request)
    {
        $this->settings->updateApprovalAndGuideSettings($request->validated());

        return back()->with('success', 'تم حفظ إعدادات الموقع.');
    }
}
