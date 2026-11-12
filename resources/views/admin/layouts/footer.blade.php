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

<script>

    // ==========================================
    // Admin Delete & Selection Manager
    // ==========================================

    window.AdminDeleteMessages = {
        title: @json(__('dashboard.Are you sure?')),
        singleText: @json(__('dashboard.This record will be deleted.')),
        bulkText: @json(__('dashboard.Selected items will be deleted!')),
        confirm: @json(__('dashboard.Yes, delete it!')),
        confirmBulk: @json(__('dashboard.Yes, delete them!')),
        cancel: @json(__('dashboard.Cancel')),
        warning: @json(__('dashboard.Warning')),
        noSelected: @json(__('dashboard.No items selected'))
    };


    // ==========================================
    // Get checkboxes belonging to a group
    // ==========================================

    function getGroupCheckboxes(group) {

        return Array.from(
            document.querySelectorAll('[data-select-item]')
        ).filter(function (checkbox) {
            return checkbox.dataset.selectItem === group;
        });
    }


    // ==========================================
    // Update Select All Checkbox
    // ==========================================

    function updateSelectAll(group) {

        const checkboxes = getGroupCheckboxes(group);

        const selectAll = Array.from(
            document.querySelectorAll('[data-select-all]')
        ).filter(function (checkbox) {
            return checkbox.dataset.selectAll === group;
        });

        const checkedCount = checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).length;

        selectAll.forEach(function (checkbox) {

            checkbox.checked =
                checkboxes.length > 0 &&
                checkedCount === checkboxes.length;

            checkbox.indeterminate =
                checkedCount > 0 &&
                checkedCount < checkboxes.length;

        });
    }


    // ==========================================
    // Select All / Select Individual
    // ==========================================

    document.addEventListener('change', function (event) {

        const target = event.target;

        // Select All
        if (target.matches('[data-select-all]')) {

            const group = target.dataset.selectAll;

            getGroupCheckboxes(group).forEach(function (checkbox) {
                checkbox.checked = target.checked;
            });

            updateSelectAll(group);
        }

        // Select Individual
        if (target.matches('[data-select-item]')) {

            updateSelectAll(target.dataset.selectItem);
        }

    });


    // ==========================================
    // Individual Delete Confirmation
    // ==========================================

    document.addEventListener('submit', function (event) {

        const form = event.target;

        if (!form.matches('[data-confirm-delete]')) {
            return;
        }

        event.preventDefault();

        const messages = window.AdminDeleteMessages;

        const confirmDelete = function () {

            if (typeof Swal === 'undefined') {
                if (window.confirm(messages.singleText)) {
                    HTMLFormElement.prototype.submit.call(form);
                }
                return;
            }

            Swal.fire({
                title: messages.title,
                text: form.dataset.confirmText || messages.singleText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: messages.confirm,
                cancelButtonText: messages.cancel
            }).then(function (result) {

                if (result.isConfirmed) {
                    HTMLFormElement.prototype.submit.call(form);
                }

            });
        };

        confirmDelete();

    });


    // ==========================================
    // Bulk Delete Confirmation
    // ==========================================

    document.addEventListener('submit', function (event) {

        const form = event.target;

        if (!form.matches('[data-bulk-delete]')) {
            return;
        }

        event.preventDefault();

        const group = form.dataset.selectionGroup;

        const checkboxes = getGroupCheckboxes(group);

        const selected = checkboxes
            .filter(function (checkbox) {
                return checkbox.checked;
            })
            .map(function (checkbox) {
                return checkbox.value;
            });

        const messages = window.AdminDeleteMessages;

        // No selected items
        if (selected.length === 0) {

            if (typeof Swal !== 'undefined') {

                Swal.fire(
                    messages.warning,
                    messages.noSelected,
                    'warning'
                );

            } else {

                window.alert(messages.noSelected);

            }

            return;
        }

        // Get hidden input containing selected IDs
        const idsInput = form.querySelector('[name="ids"]');

        if (!idsInput) {
            console.error('Bulk delete form requires an input named "ids".');
            return;
        }

        // Put IDs inside hidden input
        idsInput.value = selected.join(',');

        // Confirmation
        if (typeof Swal === 'undefined') {

            if (window.confirm(messages.bulkText)) {
                HTMLFormElement.prototype.submit.call(form);
            }

            return;
        }

        Swal.fire({
            title: messages.title,
            text: messages.bulkText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: messages.confirmBulk,
            cancelButtonText: messages.cancel
        }).then(function (result) {

            if (result.isConfirmed) {
                HTMLFormElement.prototype.submit.call(form);
            }

        });

    });

</script>


</body>
</html>

@yield('script')


</body>
</html>
