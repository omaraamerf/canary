{{-- Saved birds still on the site; the script drops ids that are missing here from the saved list. --}}
@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach
