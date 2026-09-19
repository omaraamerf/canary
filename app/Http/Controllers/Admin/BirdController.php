<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bird;
use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class BirdController extends Controller
{
    public function index()
    {
        return view('admin.birds.index', [
            'birds' => Bird::with(['breed', 'media'])->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.birds.form', [
            'bird' => new Bird(),
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $bird = Bird::create([
                ...Arr::except($data, ['image_urls', 'video_urls']),
                'featured' => $request->boolean('featured'),
                'seller_id' => $request->user()->id,
                'slug' => $this->uniqueSlug($data['title']),
            ]);
            $this->syncMedia($bird, $request);
        });

        return redirect()->route('admin.birds.index')->with('success', 'تمت إضافة الطائر.');
    }

    public function edit(Bird $bird)
    {
        $bird->load('media');

        return view('admin.birds.form', [
            'bird' => $bird,
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Bird $bird)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $bird, $data) {
            $bird->update([
                ...Arr::except($data, ['image_urls', 'video_urls']),
                'featured' => $request->boolean('featured'),
                'slug' => $bird->title === $data['title'] ? $bird->slug : $this->uniqueSlug($data['title'], $bird->id),
            ]);
            $this->syncMedia($bird, $request);
        });

        return redirect()->route('admin.birds.index')->with('success', 'تم تحديث الإعلان.');
    }

    public function destroy(Bird $bird)
    {
        abort_if($bird->orders()->exists(), 422, 'لا يمكن حذف طائر مرتبط بطلبات. غيّر حالته بدلًا من ذلك.');
        $bird->delete();

        return redirect()->route('admin.birds.index')->with('success', 'تم حذف الإعلان.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'breed_id' => ['required', 'exists:breeds,id'],
            'title' => ['required', 'string', 'max:180'],
            'sex' => ['required', 'in:male,female,unknown'],
            'hatch_year' => ['nullable', 'integer', 'min:2000', 'max:'.now()->year],
            'color' => ['required', 'string', 'max:80'],
            'molt_status' => ['nullable', 'in:ready,young,molting'],
            'breeding_ready' => ['nullable', 'boolean'],
            'singing_status' => ['nullable', 'in:singing,not_singing,young,female'],
            'ring_number' => ['nullable', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'city' => ['required', 'string', 'max:100'],
            'delivery_type' => ['required', 'in:pickup,delivery,agreement'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:available,reserved,sold'],
            'featured' => ['nullable', 'boolean'],
            'image_urls' => ['required', 'string'],
            'video_urls' => ['nullable', 'string'],
        ]);
    }

    private function syncMedia(Bird $bird, Request $request): void
    {
        $images = $this->lines($request->string('image_urls')->toString());
        $videos = $this->lines($request->string('video_urls')->toString());

        if ($images === []) {
            throw ValidationException::withMessages(['image_urls' => 'أضف رابط صورة واحدًا على الأقل.']);
        }

        foreach ($videos as $video) {
            if (! str_contains(parse_url($video, PHP_URL_HOST) ?? '', 'drive.google.com')) {
                throw ValidationException::withMessages(['video_urls' => 'روابط الفيديو يجب أن تكون من Google Drive.']);
            }
        }

        $bird->media()->delete();
        $position = 0;

        foreach ($images as $image) {
            $bird->media()->create(['type' => 'image', 'url' => $image, 'sort_order' => $position++]);
        }

        foreach ($videos as $video) {
            $bird->media()->create(['type' => 'video', 'url' => $video, 'sort_order' => $position++]);
        }
    }

    private function lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value))));
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $base = Str::slug($title) ?: 'bird';
        $slug = $base;
        $counter = 2;

        while (Bird::withTrashed()->where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
