@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-check remember me checkbox on admin login
    const rememberCheckbox = document.querySelector('input[name="remember"]');
    if (rememberCheckbox && !rememberCheckbox.checked) {
        rememberCheckbox.checked = true;
    }
});
</script>
@endpush
