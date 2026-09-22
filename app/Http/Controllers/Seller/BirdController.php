<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\SaveBirdRequest;
use App\Models\Bird;
use App\Models\Breed;
use App\Services\BirdService;
use Illuminate\Http\Request;

class BirdController extends Controller
{
    public function __construct(private readonly BirdService $birds) {}

    public function index(Request $request)
    {
        return view('seller.birds.index', [
            'birds' => $request->user()->birds()->with(['breed', 'media', 'region'])->latest()->paginate(15),
        ]);
    }

    public function create(Request $request)
    {
        return view('seller.birds.form', [
            'bird' => new Bird,
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'region' => $request->user()->sellerProfile->region,
        ]);
    }

    public function store(SaveBirdRequest $request)
    {
        $bird = $this->birds->createForSeller($request->validated(), $request->user());

        $message = $bird->approval_status === ApprovalStatus::Approved->value
            ? 'تم نشر الإعلان.'
            : 'تم حفظ الإعلان وإرساله للمراجعة.';

        return redirect()->route('seller.birds.index')->with('success', $message);
    }

    public function edit(Request $request, Bird $bird)
    {
        $this->owns($request, $bird);
        $bird->load('media');

        return view('seller.birds.form', [
            'bird' => $bird,
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'region' => $request->user()->sellerProfile->region,
        ]);
    }

    public function update(SaveBirdRequest $request, Bird $bird)
    {
        $this->owns($request, $bird);
        $bird = $this->birds->updateForSeller($bird, $request->validated());

        $message = $bird->approval_status === ApprovalStatus::Approved->value
            ? 'تم تحديث الإعلان.'
            : 'تم تحديث الإعلان وإرساله للمراجعة.';

        return redirect()->route('seller.birds.index')->with('success', $message);
    }

    public function destroy(Request $request, Bird $bird)
    {
        $this->owns($request, $bird);
        abort_if($bird->orders()->exists(), 422, 'لا يمكن حذف إعلان مرتبط بطلبات.');
        $bird->delete();

        return redirect()->route('seller.birds.index')->with('success', 'تم حذف الإعلان.');
    }

    private function owns(Request $request, Bird $bird): void
    {
        abort_unless($bird->seller_id === $request->user()->id, 403);
    }
}
