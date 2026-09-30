<script type="application/json" id="location-data">{!! json_encode(app(\App\Support\LocationOptions::class)->toArray(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>
