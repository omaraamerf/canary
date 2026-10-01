<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use Illuminate\Http\Request;

/**
 * Favourites live in the visitor's browser (localStorage), so they work without an account.
 * The page is a shell; resources/js/favorites.js asks cards() for the saved birds.
 */
class FavoriteController extends Controller
{
    private const MAX = 60;

    public function index()
    {
        return view('favorites.index');
    }

    public function cards(Request $request)
    {
        $ids = collect(explode(',', $request->string('ids')->toString()))
            ->map(fn (string $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->take(self::MAX);

        $birds = $ids->isEmpty() ? collect() : Bird::with(['breed', 'media', 'region', 'seller.sellerProfile'])
            ->published()
            ->whereIn('id', $ids)
            ->get()
            // Most recently saved first: the order the browser keeps them in, reversed.
            ->sortBy(fn (Bird $bird): int => -$ids->search($bird->id))
            ->values();

        return view('favorites.cards', ['birds' => $birds]);
    }
}
