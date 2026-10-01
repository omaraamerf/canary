{{-- A modal <dialog>: bottom sheet on phones, centered panel elsewhere. Open it with data-sheet-open="{id}". --}}
@props(['id', 'title'])
<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" {{ $attributes->class('sheet') }}>
    <div class="sheet-head">
        <h2 id="{{ $id }}-title">{{ $title }}</h2>
        <form method="dialog"><x-ui.icon-button type="submit" icon="x" :label="__('ui.layout.close')" variant="ghost" /></form>
    </div>
    <div class="sheet-body">{{ $slot }}</div>
</dialog>
