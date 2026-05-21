</div> <!-- .wrapper -->


<script src="{{asset('dashboard')}}/js/jquery.min.js"></script>


<script src="{{asset('dashboard')}}/js/popper.min.js"></script>
<script src="{{asset('dashboard')}}/js/bootstrap.min.js"></script>


<script src="{{asset('dashboard')}}/js/moment.min.js"></script>
<script src="{{asset('dashboard')}}/js/simplebar.min.js"></script>
<script src="{{asset('dashboard')}}/js/daterangepicker.js"></script>
<script src="{{asset('dashboard')}}/js/jquery.stickOnScroll.js"></script>
<script src="{{asset('dashboard')}}/js/tinycolor-min.js"></script>
<script src="{{asset('dashboard')}}/js/config.js"></script>
<script src="{{asset('dashboard')}}/js/jquery.dataTables.min.js"></script>
<script src="{{asset('dashboard')}}/js/dataTables.bootstrap4.min.js"></script>
<script src="{{asset('dashboard')}}/js/sweetalert2.js"></script>
<script src="{{asset('dashboard')}}/js/Sortable.min.js"></script>
<script src="{{asset('dashboard')}}/js/quill.min.js"></script>
<script src="{{asset('dashboard')}}/js/select2.min.js"></script>
<script src="{{ asset('dashboard')}}/ckeditor/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        // تشغيل select2
        $('#courseSelect').select2({
            placeholder: "--- {{ __('dashboard.Select Course') }} ---",
            allowClear: true
        });

        // تشغيل DataTable
        $('#dataTable-1').DataTable({
            autoWidth: true,
            "lengthMenu": [
                [16, 32, 64, -1],
                [16, 32, 64, "All"]
            ]
        });
    });
</script>

<script src="{{asset('dashboard')}}/js/apps.js"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');
</script>

@yield('script')
@stack('script')

</body>
</html>
