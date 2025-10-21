var table = $('.datatables-active:not(.check-disabled)').DataTable({
    'columnDefs': [{
        'targets': 0,
        'searchable': false,
        'orderable': false,
        'className': 'dt-body-center',
        'render': function (data, type, full, meta) {
            var rowId = meta.row;
            return `
                <div class="dt-checkbox">
                    <input type="checkbox" id="checkbox-${rowId}" name="id[]" value="${data}">
                    <label for="checkbox-${rowId}" class="visual-checkbox"></label>
                </div>
            `;
        }
    }],
    info: false,
    paging: false,
    searching: false,
    autoWidth: true,
    "oPaginate": {
        "previous": "Previous page"
    },
    "bLengthChange": false,
    'order': [[1, 'asc']]
});

// Handle click on "Select all" control
$('#selectAll').on('change', function () {
    // Get all rows with search applied
    var rows = table.rows({ 'search': 'applied' }).nodes();
    // Check/uncheck checkboxes for all rows in the table
    $('input[type="checkbox"]', rows).prop('checked', this.checked);

    // The number of checked checkboxes in the data tables
    updateSelectedCount();
});

// Handle click on checkbox to set state of "Select all" control
$('.datatables-active tbody').on('change', 'input[type="checkbox"]', function () {
    // If checkbox is not checked
    if (!this.checked) {
        var el = $('#selectAll').get(0);
        // If "Select all" control is checked and has 'indeterminate' property
        if (el && el.checked && ('indeterminate' in el)) {
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
        }
    }

    // The number of checked checkboxes in the data tables
    updateSelectedCount();
});

// Handle form submission event
$('#frm-example').on('submit', function (e) {
    var form = this;

    // Iterate over all checkboxes in the table
    table.$('input[type="checkbox"]').each(function () {
        // If checkbox doesn't exist in DOM
        if (!$.contains(document, this)) {
            // If checkbox is checked
            if (this.checked) {
                // Create a hidden element
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .val(this.value)
                );
            }
        }
    });
});

function updateSelectedCount() {
    var numChecked = $('.datatables tbody input[type="checkbox"]').not('#selectAll').filter(':checked').length;
    if (numChecked) {
        $('#checks-count').html(`${numChecked} Selected`);
    } else {
        $('#checks-count').html('');
    }
    updateButtonVisibility();
}

function updateButtonVisibility() {
    // The number of checked checkboxes in the data tables
    var numChecked = $('.datatables tbody input[type="checkbox"]').not('#selectAll').filter(':checked').length;

    // Toggle the visibility of the buttons
    if (numChecked > 0) {
        $('.onchange-visible').show();
        $('.onchange-hidden').hide();
    } else {
        $('.onchange-visible').hide();
        $('.onchange-hidden').show();
        $('#selectAll').prop('checked', '');
    }
}

const container = document.querySelector('.dataTables_wrapper .row:nth-child(2) [class*="col-"]');
if (container) {
    container.addEventListener('wheel', (event) => {
        if (container.scrollLeft === 0 && event.deltaY < 0) {
            return;
        } else if (container.scrollLeft === container.scrollWidth - container.clientWidth && event.deltaY > 0) {
            return;
        }
        event.preventDefault();
        container.scrollLeft += event.deltaY;
    });
}
