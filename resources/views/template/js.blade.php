<script src="{{ asset('template/assets/static/js/components/dark.js') }}"></script>
<script src="{{ asset('template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>


<script src="{{ asset('template/assets/compiled/js/app.js') }}"></script>



<!-- Need: Apexcharts -->
{{-- <script src="{{ asset('template/assets/extensions/apexcharts/apexcharts.min.js') }}"></script> --}}
{{-- <script src="{{ asset('template/assets/static/js/pages/dashboard.js') }}"></script> --}}
<script src="{{ asset('template/assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>
<script src="{{ asset('template/assets/static/js/pages/form-element-select.js') }}"></script>


<script src="{{ asset('template/assets/extensions/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('template/assets/static/js/pages/datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"
    integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.min.js"
    integrity="sha512-FbWDiO6LEOsPMMxeEvwrJPNzc0cinzzC0cB/+I2NFlfBPFlZJ3JHSYJBtdK7PhMn0VQlCY1qxflEG+rplMwGUg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>



<style>
    .readonlyInput {
        background-color: #e9ecef
    }
</style>
<script>
    $('.select_option').select2({
        theme: "bootstrap-5",
        width: "100%"
    });

    $('.select_modal').select2({
        dropdownParent: $('#modal_cetak .modal-content'),
        theme: 'bootstrap-5'
    });

    function tanggalIndonesia(date) {
        const event = new Date(date);
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        };

        return event.toLocaleDateString('id-ID', options)
    }
</script>


@stack('js')
