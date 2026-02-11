/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
// for pos system
var session_key = $(location).attr("href").split('/').pop();
//

$(function () {
    if ($('.custom-scroll').length) {
        $(".custom-scroll").niceScroll();
        $(".custom-scroll-horizontal").niceScroll();
    }
    // loadConfirm();

});
// DataTables Momen Mohsen 2024-06-16
$(document).ready(function () {

    if (!$.fn.dataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable({

            dom: '<"d-flex justify-content-between top-table-bar"l<"flex-fill search-top-bar"f>B>tip',
            select: true,
            autoWidth: true,

            buttons: [{
                extend: 'copy',
                exportOptions: {
                    // columns: ':not(:last-child)'
                },
                text: ' copy <i class="ti ti-copy pe-1"></i>',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                exportOptions: {
                    // columns: ':not(:last-child)'
                },
                text: ' csv <i class="ti ti-table pe-1"></i>',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'excel',
                exportOptions: {
                    // columns: ':not(:last-child)'
                },
                text: ' excel <i class="ti ti-table pe-1"></i>',
                className: 'btn btn-sm btn-success text-white'
            },
            {
                extend: 'print',
                autoPrint: true,
                exportOptions: {
                    // columns: ':not(:last-child)'
                },

                customize: function (win) {
                    $(win.document.body).find('h1').addClass('Title').css('display', 'none');
                    $(win.document.body).find('td').addClass('custom-width').css('custom-width', '100px');

                    $.ajax({
                        url: 'userinfo/img',
                        method: 'GET',
                        success: function (response) {
                            // Assuming the response contains the image URL and settings data
                            // Get language and direction
                            const lang = $('html').attr('lang');
                            const dir = $('html').attr('dir');
                            const isArabic = dir === 'rtl';



                            const $data = $('.data');

                            // Retrieve data attributes
                            const customerData = $data.data('account') || {};
                            const filterData = $data.data('filter') || "Date Unavailable";
                            const accountType = $data.data('type-select') || '';

                            console.log(customerData);

                            // Translation fallback
                            const customerName = customerData.user_name || customerData.account_name || '{{ __("No Data Available.!") }}';

                            // Text blocks
                            const text = {
                                statementFor: isArabic ? 'كشف حساب لـ: ' : 'Account Statement for: ',
                                filterType: isArabic ? 'نوع الكشف: ' : 'Filter Type: ',
                                accountId: isArabic ? 'رقم الحساب: ' : 'Account ID: ',
                                from: isArabic ? 'من : ' : 'From: ',
                                to: isArabic ? ' إلى : ' : 'To: '
                            };
                            var now = new Date();
                            const formattedDate = now.toLocaleDateString(); // e.g., "6/24/2024
                            const companyInfo = `
                                <div class="row col-md-12 text-center">
                                    <div class="col-md-12">
                                        <h6>${text.statementFor} ${customerName}</h6>
                                        <h6>${text.filterType} ${accountType}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <h6>${text.accountId} ${customerData.account_id || ''}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <h6>${text.from}${filterData.startDateRange || formattedDate}${text.to} ${filterData.endDateRange || formattedDate} </h6>
                                    </div>
                                </div>
                            `;
                            $(win.document.body).prepend(companyInfo);


                            var background_image = '<img class="image-logo-holol" src="' + response.background + '" alt="Company Logo" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;width: 100%;height: 100%;opacity: 0.1;object-fit: contain;">';
                            var logo_image = '<img src="' + response.logo + '" alt="Company Logo" >';
                            var settings_data = response.settings;

                            $(win.document.body).prepend(background_image);
                            $(win.document.body).prepend('<div class="top-header test  row"></div>');
                            $(win.document.body).prepend('<div class="top-info d-flex  row"></div>');
                            $(win.document.body).prepend('<div class="top-title d-flex  row"></div>');
                            $(win.document.body).prepend('<div class="top-date d-flex  row"></div>');
                            $(win.document.body).find('.top-info').prepend('<div class="m-2"  style=" width: 100%;max-width: 130px;" >' + settings_data + '</div>');
                            $(win.document.body).find('.top-header').prepend('<div class="information"    style=" display:flex;justify-content:center;align-items:center; text-align: center;">' + logo_image + '</div>');
                            // Add social media icons
                            // Social media icons with responsive styling

                            $(win.document.body).find('.top-title').append('<div class=" cols   text-center align-items-center" style="position: absolute; top: 15px;">' + document.title + '</div>');
                            // Add current date and time
                            var formattedDateTime = now.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'numeric',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            $(win.document.body).find('.top-date').append('<div class="" style="margin-left:5px;position: absolute; top: 10px;">' + formattedDateTime + '</div>');


                            let table = $(win.document.body).find('.datatable');
                            let columns = table.find('thead .closing_balance');
                            if (dir == 'rtl') {
                                table.attr('dir', 'rtl');
                                $(win.document.body).find('.top-date').attr('dir', 'ltr').addClass('text-start');
                            } else {
                                table.attr('dir', 'ltr');
                                $(win.document.body).find('.top-date').attr('dir', 'ltr').addClass('text-end');
                            }
                            table.addClass('table-bordered');
                            $(win.document.body).prepend(pdfHeader);
                            let updatedText = fullText.replace("The Future ERP - ", "ddddd");
                            titleElement.text(updatedText);
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText); // عرض الخطأ في وحدة التحكم
                            alert('Failed to fetch image data.');
                        }
                    });
                },
                text: ' print <i class="ti ti-printer pe-1"></i>',
                className: 'btn btn-sm btn-danger text-white'
            }
            ],
            lengthMenu: [
                [15, 30, 50, 70, 100, 1000000],
                [15, 30, 50, 70, 100, 'All']
            ],
            order: []
        });
    }
});

// datatable END
function daterange() {
    if ($("#pc-daterangepicker-1").length > 0) {
        document.querySelector("#pc-daterangepicker-1").flatpickr({
            mode: "range"
        });
    }
}


function select2() {
    if ($(".select2").length > 0) {
        $($(".select2")).each(function (index, element) {
            var id = $(element).attr('id');

            // Initialize Choices.js for each select element
            var multipleCancelButton = new Choices('#' + id, {
                shouldSort: false,
                removeItemButton: true,
            });

            // Listen for a change event on the hidden select element
            $("#" + id).on('change', function () {
                var selectElement = $(this);

                // Check if a value is selected
                if (selectElement.val()) {
                    // If a value is selected, add 'is-valid' class
                    selectElement.removeClass('is-invalid').addClass('is-valid');
                } else {
                    // If no value is selected, add 'is-invalid' class
                    selectElement.removeClass('is-valid').addClass('is-invalid');
                }
            });

            // Ensure initial validation (for pre-selected or no selection state)
            var selectElement = $("#" + id);
            if (selectElement.val()) {
                selectElement.addClass('is-valid').removeClass('is-invalid');
            } else {
                selectElement.addClass('is-invalid').removeClass('is-valid');
            }

            // Apply validation classes to the input field of Choices.js as well
            $("#" + id).next(".choices").find(".choices__input").on('input', function () {
                var inputElement = $(this);
                var parentSelect = $("#" + id);

                // Trigger change on the actual hidden select
                parentSelect.trigger('change');

                // Apply validation class to the input
                if (parentSelect.val()) {
                    inputElement.removeClass('is-invalid').addClass('is-valid');
                } else {
                    inputElement.removeClass('is-valid').addClass('is-invalid');
                }
            });
        });
    }
}

// Add a function to validate on form submission
function validateFormOnSubmit(event) {
    // Loop through all .select2 elements and validate each one
    $(".select2").each(function () {
        var selectElement = $(this);
        if (selectElement.val()) {
            selectElement.removeClass('is-invalid').addClass('is-valid');
        } else {
            selectElement.removeClass('is-valid').addClass('is-invalid');
            // You can also show custom validation message
            selectElement.next('.invalid-feedback').show(); // Show validation error
        }
    });

    // Prevent form submission if there is any invalid field
    // if ($(".is-invalid").length > 0) {
    //     event.preventDefault();  // Prevent form submission
    // }
}

// Trigger the validation when the form is submitted
$("form").on('submit', function (event) {
    validateFormOnSubmit(event); // Validate all select2 fields before submission
});


function show_toastr(type, message) {

    var f = document.getElementById('liveToast');
    var a = new bootstrap.Toast(f).show();
    if (type == 'success') {
        $('#liveToast').addClass('bg-primary');
    } else if ((type == 'error')) {
        $('#liveToast').addClass('bg-danger');
    } else if ((type == 'w-error')) {
        $('#liveToast').addClass('bg-danger fixed-top w-100 text-center');
        $('#liveToast .toast-body').addClass('w-100');
    } else if ((type == 'warning')) {
        $('#liveToast').addClass('bg-warning');
    } else if ((type == 'w-warning')) {
        $('#liveToast').addClass('bg-warning fixed-top w-100 text-center');
        $('#liveToast .toast-body').addClass('w-100');
    }
    $('#liveToast .toast-body').html(message);
}

$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]', function () {

    var data = {};
    var title1 = $(this).data("title");

    var title2 = $(this).data("bs-original-title");
    var title3 = $(this).data("original-title");
    var title = (title1 != undefined) ? title1 : title2;
    var title = (title != undefined) ? title : title3;

    $('.modal-dialog').removeClass('modal-xl');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);

    if ($('#vc_name_hidden').length > 0) {
        data['vc_name'] = $('#vc_name_hidden').val();
    }
    if ($('#warehouse_name_hidden').length > 0) {
        data['warehouse_name'] = $('#warehouse_name_hidden').val();
    }
    if ($('#discount_hidden').length > 0) {
        data['discount'] = $('#discount_hidden').val();
    }
    if ($('#quotation_id').length > 0) {
        data['quotation_id'] = $('#quotation_id').val();
    }
    $.ajax({
        url: url,
        data: data,
        success: function (data) {
            $('#commonModal .body').html(data);
            $("#commonModal").modal('show');
            // daterange_set();
            taskCheckbox();
            common_bind("#commonModal");
            commonLoader();

        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});


function arrayToJson(form) {
    var data = $(form).serializeArray();
    var indexed_array = {};

    $.map(data, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}


function common_bind() {
    select2();
}


function taskCheckbox() {
    var checked = 0;
    var count = 0;
    var percentage = 0;

    count = $("#check-list input[type=checkbox]").length;
    checked = $("#check-list input[type=checkbox]:checked").length;
    percentage = parseInt(((checked / count) * 100), 10);
    if (isNaN(percentage)) {
        percentage = 0;
    }
    $(".custom-label").text(percentage + "%");
    $('#taskProgress').css('width', percentage + '%');


    $('#taskProgress').removeClass('bg-warning');
    $('#taskProgress').removeClass('bg-primary');
    $('#taskProgress').removeClass('bg-success');
    $('#taskProgress').removeClass('bg-danger');

    if (percentage <= 15) {
        $('#taskProgress').addClass('bg-danger');
    } else if (percentage > 15 && percentage <= 33) {
        $('#taskProgress').addClass('bg-warning');
    } else if (percentage > 33 && percentage <= 70) {
        $('#taskProgress').addClass('bg-primary');
    } else {
        $('#taskProgress').addClass('bg-success');
    }
}


function commonLoader() {
    $('[data-toggle="tooltip"]').tooltip();
    if ($('[data-toggle="tags"]').length > 0) {
        $('[data-toggle="tags"]').tagsinput({
            tagClass: "badge badge-primary"
        });
    }


    var e = $(".scrollbar-inner");
    e.length && e.scrollbar().scrollLock()

    var e1 = $(".custom-input-file");
    e1.length && e1.each(function () {
        var e1 = $(this);
        e1.on("change", function (t) {
            ! function (e, t, a) {
                var n, o = e.next("label"),
                    i = o.html();
                t && t.files.length > 1 ? n = (t.getAttribute("data-multiple-caption") || "").replace("{count}", t.files.length) : a.target.value && (n = a.target.value.split("\\").pop()), n ? o.find("span").html(n) : o.html(i)
            }(e1, this, t)
        }), e1.on("focus", function () {
            ! function (e) {
                e.addClass("has-focus")
            }(e1)
        }).on("blur", function () {
            ! function (e) {
                e.removeClass("has-focus")
            }(e1)
        })
    })

    // var e2 = $('[data-toggle="autosize"]');
    // e2.length && autosize(e2);


    if ($(".jscolor").length) {
        jscolor.installByClassName("jscolor");
    }
    summernote();
    // for Choose file
    $(document).on('change', 'input[type=file]', function () {
        var fileclass = $(this).attr('data-filename');
        var finalname = $(this).val().split('\\').pop();
        $('.' + fileclass).html(finalname);
    });
}


function summernote() {
    if ($(".summernote-simple").length) {
        $('.summernote-simple').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });
        $('.dropdown-toggle').dropdown();
    }



    if ($(".summernote-simple-2").length) {
        $('.summernote-simple-2').summernote({
            dialogsInBody: !0,
            minHeight: 200,
            maxHeight: 300,
            toolbar: [
                ['style', ['style']],
                ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ["para", ["ul", "ol", "paragraph"]],
            ],

        });
    }

    if ($(".summernote-simple-3").length) {
        $('.summernote-simple-3').summernote();
    }



}



$(document).on("click", '.bs-pass-para', function () {
    var form = $(this).closest("form");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "This action can not be undone. Do you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            form.submit();

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) { }
    })
});

//only pos system delete button
$(document).on("click", '.bs-pass-para-pos', function () {

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "This action can not be undone. Do you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            document.getElementById($(this).data('confirm-yes')).submit();

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) { }
    })
});


function postAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = {
        _token: token
    };

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}

//end only pos system delete button


function deleteAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = {
        _token: token
    };

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'DELETE',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}

// Google calendar
$(document).on('click', '.local_calender .fc-daygrid-event, .fc-timegrid-event', function (e) {
    // if (!$(this).hasClass('project')) {
    e.preventDefault();
    var event = $(this);
    var title1 = $('.fc-event-title').html();
    var title2 = $(this).data("bs-original-title");
    var title = (title1 != undefined) ? title1 : title2;
    // var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var size = 'md';
    var url = $(this).attr('href');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function (data) {
            $('#commonModal .body').html(data);
            $("#commonModal").modal('show');
            common_bind();
        },
        error: function (data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
    // }
});

//date value 4

// $(function(){
//
//     var dtToday = new Date();
//
//     var month = dtToday.getMonth() + 1;
//     var day = dtToday.getDate();
//     var year = dtToday.getFullYear();
//     if(month < 10)
//         month = '0' + month.toString();
//     if(day < 10)
//         day = '0' + day.toString();
//
//     var maxDate = year + '-' + month + '-' + day;
//
//     $("input[type='date']").attr('max', maxDate);
// });

function addCommas(num) {
    var number = parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    return ((site_currency_symbol_position == "pre") ? site_currency_symbol : '') + number + ((site_currency_symbol_position == "post") ? site_currency_symbol : '');
}



// PLUS MINUS QUANTITY JS
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function (a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"),
            c.children().first().before('<input type="button" value="-" class="minus" />'),
            c.children().last().after('<input type="button" value="+" class="plus" />')
    })
}

String.prototype.getDecimals || (String.prototype.getDecimals = function () {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function () {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function () {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".plus, .minus", function () {
    var a = jQuery(this).closest(".quantity").find('input[name="quantity"], input[name="quantity[]"]'),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});

$(document).on('click', 'input[name="quantity"], input[name="quantity[]"]', function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});


//for ai module
$(document).on('click', 'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]', function () {
    var validate = $(this).attr('data-validate');
    var id = '';
    if (validate) {
        id = $(validate).val();
    }
    var title_over = $(this).data('title');
    $('#commonModalOver .modal-dialog').removeClass('modal-lg');
    var size_over = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModalOver .modal-title").html(title_over);
    $("#commonModalOver .modal-dialog").addClass('modal-' + size_over);
    $.ajax({
        url: url + '?id=' + id,
        success: function (data) {
            $('#commonModalOver .modal-body').html(data);
            $("#commonModalOver").modal('show');
            taskCheckbox();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });
});



//start input serach box
function JsSearchBox() {
    if ($(".js-searchBox").length) {
        $(".js-searchBox").each(function (index) {
            if ($(this).parent().find('.formTextbox').length == 0) {
                $(this).searchBox({
                    elementWidth: '250'
                });
            }
        });
    }
}

$(document).ready(function () {
    JsSearchBox();

    function JsSearchBox() {
        if ($(".js-searchBox").length) {
            $(".js-searchBox").each(function (index) {
                if ($(this).parent().find('.formTextbox').length === 0) {
                    $(this).searchBox({
                        elementWidth: '250'
                    });
                }
            });
        }
    }
});

//end input serach box




var
    swich_monthly = document.getElementById("filt-monthly"),
    swich_yearly = document.getElementById("filt-yearly"),
    Swich_hour_month = document.getElementById("switcher"),
    MonthPlans = document.getElementById("monthly"),
    YearlyPlans = document.getElementById("yearly");


if (Swich_hour_month) {

    Swich_hour_month.addEventListener("click", function () {
        if (swich_yearly.classList.contains("text-secondary")) {
            swich_yearly.classList.remove("text-secondary");
            swich_monthly.classList.add("text-secondary");
            MonthPlans.classList.add("d-none");
            YearlyPlans.classList.remove("d-none");
        } else {
            swich_yearly.classList.add("text-secondary");
            swich_monthly.classList.remove("text-secondary");
            MonthPlans.classList.remove("d-none");
            YearlyPlans.classList.add("d-none");
        }


    });
}



//created 12/8 func to switch currency between egp and usd
var
    switcUSD = document.getElementById("filt-usd"),
    switchEGP = document.getElementById("filt-egp"),
    switchCurrency = document.getElementById("currency-switch"),
    USDprice = document.querySelectorAll(".dollar"),
    EGPprice = document.querySelectorAll(".pound-egy");

if (switcUSD && switchEGP && switchCurrency && USDprice && EGPprice) {
    if (!switcUSD.classList.contains("text-secondary")) {
        USDprice.forEach(function (el) {
            el.classList.remove("d-none");
        });
        EGPprice.forEach(function (el) {
            el.classList.add("d-none");
        });
    } else {
        USDprice.forEach(function (el) {
            el.classList.add("d-none");
        });
        EGPprice.forEach(function (el) {
            el.classList.remove("d-none");
        });
    }

    switchCurrency.addEventListener("click", function () {
        if (switcUSD.classList.contains("text-secondary")) {
            switcUSD.classList.remove("text-secondary");
            switchEGP.classList.add("text-secondary");
            USDprice.forEach(function (el) {
                el.classList.remove("d-none");
            });
            EGPprice.forEach(function (el) {
                el.classList.add("d-none");
            });
        } else {
            switcUSD.classList.add("text-secondary");
            switchEGP.classList.remove("text-secondary");
            USDprice.forEach(function (el) {
                el.classList.add("d-none");
            });
            EGPprice.forEach(function (el) {
                el.classList.remove("d-none");
            });
        }
    });


}




$(document).ready(function () {
    summernote();
});
