<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBirdRequest;
use App\Http\Requests\Admin\UpdateBirdApprovalRequest;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Services\BirdService;
use Illuminate\Http\Request;

class BirdController extends Controller
{
    public function __construct(private readonly BirdService $birds) {}

    public function index(Request $request)
    {
        return view('admin.birds.index', [
            'birds' => Bird::with(['breed', 'media', 'region', 'seller.sellerProfile'])
                ->when($request->filled('approval'), fn ($query) => $query->where('approval_status', $request->string('approval')))
                ->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.birds.form', [
            'bird' => new Bird,
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(SaveBirdRequest $request)
    {
        $this->birds->createForAdmin($request->validated(), $request->user());

        return redirect()->route('admin.birds.index')->with('success', __('تمت إضافة الطائر.'));
    }

    public function edit(Bird $bird)
    {
        $bird->load('media');

        return view('admin.birds.form', [
            'bird' => $bird,
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(SaveBirdRequest $request, Bird $bird)
    {
        $this->birds->updateForAdmin($bird, $request->validated());

        return redirect()->route('admin.birds.index')->with('success', __('تم تحديث الإعلان.'));
    }

    public function destroy(Bird $bird)
    {
        abort_if($bird->orders()->exists(), 422, __('لا يمكن حذف طائر مرتبط بطلبات. غيّر حالته بدلًا من ذلك.'));
        $bird->delete();

        return redirect()->route('admin.birds.index')->with('success', __('تم حذف الإعلان.'));
    }

    public function updateApproval(UpdateBirdApprovalRequest $request, Bird $bird)
    {
        $data = $request->validated();
        $this->birds->updateApproval(
            $bird,
            ApprovalStatus::from($data['approval_status']),
            $data['rejection_reason'] ?? null,
        );

        return back()->with('success', __('تم تحديث حالة مراجعة الإعلان.'));
    }
}
