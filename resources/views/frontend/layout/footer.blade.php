{{-- <script src="{{ asset('js/script.js') }}"></script> --}}
<script>
    window.currentUserId = {{ Auth::user()->id ?? 'null' }};
</script>