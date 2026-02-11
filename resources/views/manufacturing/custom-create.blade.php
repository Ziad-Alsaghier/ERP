<form id="calculateform">
    @csrf
    @method('POST')
<div class="modal-body">
    <!-- Full Measure Form -->
    <div class="row" id="full_measure">
        <div class="form-group col-md-12">
        {{ Form::label('Category', __('Category'), ['class'=>'form-label']) }}
        <select name="category" class="form-control" id="" >
            <option value="" selected disabled>{{__('select')}}</option>
            @foreach($categories as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>
        </div>

            <div class="form-group col-md-12" id="product"></div>
            <div class="row" id="quantity"></div>
            <div class="row" id="size"></div>
            <div class="row" id="selection"></div>
            <div class="row" id="addons"></div>
            <div class="row" id="design_info"></div>
            <div class="row" id="result"></div>

    </div>
</div>

<div class="modal-footer" id="footer_calculater">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
</div>
</form>

<script>
$(document).ready(function () {

    // عند تغيير الفئة
    $(document).on('change', 'select[name="category"]', function () {
                    $('#product').empty();
                    $('#quantity').empty();
                    $('#size').empty();
                    $('#selection').empty();
                    $('#addons').empty();
                    $('#design_info').empty();
                    $('#footeraction').empty();
                    $('#result').empty();



        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: '{{ route('manufacturing.getProductsByCategory', ':id') }}'.replace(':id', categoryId),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // console.log(data);

                    if (!data || data.length === 0) {
                        alert(`{{ __('No data') }}`);
                    }else{
                        $('select[name="product"]').empty();
                        $('#product').html(`
                            <label for="Product" class="form-label">{{ __('Product')}}</label>
                            <select name="product" class="form-control" ></select>
                        `);
                        $('select[name="product"]').append('<option selected disabled value="">{{ __("select") }}</option>'); // إضافة خيار افتراضي
                    }

                    $.each(data, function (key, value) {
                        $('select[name="product"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
                error: function (xhr, status, error) {
                    console.log("Error in category AJAX: ", error); // لرصد الأخطاء
                }
            });
        } else {
            $('select[name="product"]').empty(); // تفريغ القائمة إذا لم يتم اختيار فئة
        }
    });

    // عند تغيير المنتج
    $(document).on('change', 'select[name="product"]', function () {
        var productID = $(this).val();
        if (productID) {
            $.ajax({
                url: '{{ route('manufacturing.getProductsDetails', ':id') }}'.replace(':id', productID),

                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // console.log(data); // عرض البيانات في وحدة التحكم
                    // console.log(data.product.product_script);
                    // console.log(data.product.pricingtype); // عرض البيانات في وحدة التحكم
                    if(data.product.pricingtype == 'unite'){
                        var pricingtype = 'unite';
                    }else if(data.product.pricingtype == 'sheet'){
                        var pricingtype = 'sheet';
                    }

                    $('#quantity').empty();
                    $('#size').empty();
                    $('#selection').empty();
                    $('#addons').empty();
                    $('#design_info').empty();
                    $('#footeraction').empty();
                    $('#result').empty();

                    {{-- ============================================================== quantity ============================================================== --}}
                        if(!data.quantities || data.quantities.length !== 0 || data.product.free_amount == 1){
                            $('#quantity').html(`
                                <hr><h4>{{ __("Quantity") }}</h4><hr>
                            `);
                        }
                        if(!data.quantities || data.quantities.length !== 0){
                            $('select[name="selective_amount"]').empty();
                            $('#quantity').append(`
                                <div class="form-group col-md-6" >
                                    <input class="form-check-input" type="radio" name="amount_select" id="radioquantitystander" value="stand">
                                    <label class="form-label" id="Quantity" for="radioquantitystander">{{ __('Stander Quantity') }}</label>
                                    <select name="selective_amount" class="form-control" id="Quantity_stander" disabled>
                                    </select>
                                </div>
                            `);
                        }

                        if(data.product.free_amount == 1){
                            $('#quantity').append(`
                                <div class="form-group col-md-6" id="quantity">
                                    <input class="form-check-input" type="radio" name="amount_select" id="radioquantityfree" value="free">
                                    <label class="form-label" id="Quantity" for="radioquantityfree">{{ __('Custom Quantity') }}</label>
                                    {{ Form::text('free_amount', null, array('class'=>'form-control','id'=>'Quantity_free','disabled')) }}
                                </div>
                            `);
                        }
                        if(pricingtype == 'sheet'){
                            $('select[name="selective_amount"]').append('<option selected disabled value="">{{ __("select") }}</option>'); // إضافة خيار افتراضي
                            $.each(data.quantities, function (key, value) {
                                $('select[name="selective_amount"]').append('<option value="' + value.sheet_amount + '">' + value.name + '</option>');
                            });
                        }else if(pricingtype == 'unite'){
                            $('select[name="selective_amount"]').append('<option selected disabled value="">{{ __("select") }}</option>'); // إضافة خيار افتراضي
                            $.each(data.quantities, function (key, value) {
                                $('select[name="selective_amount"]').append('<option value="' + value.id + '">' + value.name_amount + '</option>');
                            });
                        }
                    {{-- ============================================================== sizes ============================================================== --}}
                    if(pricingtype == 'sheet'){
                        if(!data.sizes || data.sizes.length !== 0 || data.product.free_size == 1){
                            $('#size').html(`
                                    <hr><h4>{{ __("Size") }}</h4><hr>
                            `);
                        }
                        if(!data.sizes || data.sizes.length !== 0){
                            $('select[name="size"]').empty();
                            $('#size').append(`
                                    <div class="form-group col-md-12" >
                                        <input class="form-check-input" type="radio" name="size_select" id="radioSizeStander" value="stand">
                                        <label class="form-label" for="radioSizeStander" >{{ __('Stander Size') }}</label>
                                        <select name="selective_size" class="form-control" id="sizeStander"  disabled>
                                        </select>
                                    </div>
                            `);
                        }
                        if(data.product.free_size == 1){
                            let scriptproduct = data.product.product_script;
                            if (scriptproduct.includes("$open_sheet = true")) {
                                $('#size').append(`
                            <div class="form-group col-md-12" >
                                <input class="form-check-input" type="radio" name="size_select" id="radioSizeFree" value="free">
                                <label class="form-label" for="radioSizeFree" >{{ __('Custom Size') }}</label>
                            </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('height', __('Height'), ['class'=>'form-label']) }}
                                        {{ Form::text('height', null, array('class'=>'form-control','disabled','id'=>'height')) }}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('width', __('Width'), ['class'=>'form-label']) }}
                                        {{ Form::text('width', null, array('class'=>'form-control','disabled','id'=>'width')) }}
                                    </div>
                            `);
                            } else {
                                $('#size').append(`
                            <div class="form-group col-md-12" >
                                <input class="form-check-input" type="radio" name="size_select" id="radioSizeFree" value="free">
                                <label class="form-label" for="radioSizeFree" >{{ __('Custom Size') }}</label>
                            </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('height', __('Height'), ['class'=>'form-label']) }}
                                        {{ Form::text('height', null, array('class'=>'form-control','disabled','id'=>'height')) }}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('width', __('Width'), ['class'=>'form-label']) }}
                                        {{ Form::text('width', null, array('class'=>'form-control','disabled','id'=>'width')) }}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('depth', __('Depth'), ['class'=>'form-label']) }}
                                        {{ Form::text('depth', null, array('class'=>'form-control','disabled','id'=>'depth')) }}
                                    </div>
                            `);
                            }



                        }

                        $('select[name="selective_size"]').append('<option selected disabled value="">{{ __("select") }}</option>'); // إضافة خيار افتراضي
                        $.each(data.sizes, function (key, value) {
                            $('select[name="selective_size"]').append('<option value="' + value.sheet_value + '">' + value.name + '</option>');
                        });
                    }
                    {{-- ============================================================== Selection ============================================================== --}}
                    if(!data.tables || data.tables.length !== 0){
                        $('#selection').html(`
                                <hr><h4>{{ __("Selection") }}</h4><hr>
                        `);
                    }

                    $.each(data.tables, function (key, value) {
                        $('#selection').append(
                            '<div class="form-group col-md-12"><label class="form-label" for="selection">' + value.name + '</label><select name="selective[]" class="form-control" id="' + value.table_id + '"><option disabled selected>{{ __("please select") . " ( " }}' + value.name + ' ) </option></select></div>'
                        );
                        $.each(data.options, function (key, value_option) {
                            if (value_option.table_id == value.table_id) {
                                $('#' + value.table_id).append('<option value="' + value_option.id + '">' + value_option.name + '</option>');
                            }
                        });
                    });
                    {{-- ============================================================== addons ============================================================== --}}

                    if(!data.addons || data.addons.length !== 0){
                    $('#addons').html(`
                            <hr><h4>{{ __("Addons") }}</h4><hr>
                    `);
                    var counter = 0;
                    $.each(data.addons, function (key, value) {
                        counter++;
                        $('#addons').append(
                            '<div class="form-check col-md-6"><input class="form-check-input" type="checkbox" name="addons[]" value="'+value.id+'" id="flexCheckChecked_' + counter + '"><label class="form-check-label" for="flexCheckChecked_' + counter + '">' + value.name + '</label></div>'
                        );
                    });
                    }
                    {{-- ============================================================== Design ============================================================== --}}

                    $('#design_info').html(`
                            <hr><h4>{{ __("Design & Information") }}</h4><hr>
                            <div class="form-group col-md-6">
                                {{Form::label('add_design',__('Add Design'),['class' => 'form-label'])}}
                                {{Form::file('add_design',array('class'=>'form-control', 'id'=>'files'))}}
                                <img id="image" class="mt-2" style="width:25%;"/>
                            </div>
                            <div class="form-group  col-md-12">
                                {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                                {{ Form::textarea('design_note', '', array('class' => 'form-control','rows'=>3 , 'placeholder'=>__('Enter Description'))) }}
                            </div>
                    `);

                    {{-- ============================================================== Footer ============================================================== --}}
                    $('#footer_calculater').empty();
                    $('#footer_calculater').append(`<input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">`)
                    $('#footer_calculater').append(`
                            <a class="btn btn-primary text-white" onclick="claculate(${productID},'${pricingtype}')"> {{ __('calculate') }}</a>
                            <div id="footeraction"></div>
                        `);
                },
                error: function (xhr, status, error) {
                    console.log("Error in product AJAX: ", error); // لرصد الأخطاء
                }
            });
        } else {
            // تفريغ الحقول إذا لم يتم اختيار منتج
            $('select[name="size"]').empty();
            $('select[name="selective_amount"]').empty();
        }
    });



    $(document).on('change', 'input[name="amount_select"]', function () {
        if ($('#radioquantitystander').is(':checked')) {
            $('#Quantity_stander').prop('disabled', false).prop('required', true);
            $('#Quantity_free').prop('disabled', true).prop('required', false);
        } else if ($('#radioquantityfree').is(':checked')) {
            $('#Quantity_stander').prop('disabled', true).prop('required', false);
            $('#Quantity_free').prop('disabled', false).prop('required', true);
        }
    });

    $(document).on('change', 'input[name="size_select"]', function () {

        if ($('#radioSizeStander').is(':checked')) {
            $('#sizeStander').prop('disabled', false).prop('required', true);

            $('input[name="height"]').prop('disabled', true).prop('required', false);
            $('input[name="width"]').prop('disabled', true).prop('required', false);
            $('input[name="depth"]').prop('disabled', true).prop('required', false);

        } else if ($('#radioSizeFree').is(':checked')) {
            $('#sizeStander').prop('disabled', true).prop('required', false);

            $('input[name="height"]').prop('disabled', false).prop('required', true);
            $('input[name="width"]').prop('disabled', false).prop('required', true);
            $('input[name="depth"]').prop('disabled', false).prop('required', true);

        }

    });

    $(document).on('click', '#descriptionview', function (e) {
    e.preventDefault();

    // الإشارة إلى الزر الذي تم النقر عليه
    let description = $(this)
        .closest('td') // تحديد الخلية (أو العنصر الأب الأقرب) إذا كنت داخل جدول
        .find('.pro_description') // البحث عن textarea المتعلقة به
        .val(); // أخذ القيمة

    if (!description) {
        alert('Please enter a description.');
        return;
    }

    let form = $('<form>', {
        action: '{{ route("proposal.workorder") }}',
        method: 'POST',
        target: '_blank' // لفتح النتيجة في نافذة جديدة
    });

    form.append($('<input>', {
        type: 'hidden',
        name: 'description',
        value: description
    }));

    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: '{{ csrf_token() }}'
    }));

    $('body').append(form);
    form.submit();
    form.remove();
});




});

        function claculate(product_id,pricingtype = 'sheet') {
            var resultShow = document.getElementById('result');
            resultShow.innerHTML = '';
            $('#footeraction').empty();
            var quantityRadios = document.getElementsByName("amount_select");
            var selectedQuantityValue = null;
            for (var i = 0; i < quantityRadios.length; i++) {
                if (quantityRadios[i].checked) {
                    selectedQuantityValue = quantityRadios[i].value;
                    break;
                }
            }

            // إذا لم يتم تحديد أي زر
            if (selectedQuantityValue === null) {
                Swal.fire({ icon: 'error', title: 'اختار الكمية'})
                quantityRadios[0].focus();
                return;
            }

            if(selectedQuantityValue === 'stand'){
                var quantityStander = document.getElementById('Quantity_stander');
                if(!quantityStander.value || quantityStander.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل الكمية فارغ'})
                    quantityStander.focus();
                    return;
                }
            }
            if(selectedQuantityValue === 'free'){
                var quantityFree = document.getElementById('Quantity_free');
                if(!quantityFree.value || quantityFree.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل الكمية فارغ'})
                    quantityFree.focus();
                    return;
                }
            }


            if(pricingtype == 'sheet'){
            var sizesRadios = document.getElementsByName("size_select");
            var selectedSizesValue = null;

            for (var i = 0; i < sizesRadios.length; i++) {
                if (sizesRadios[i].checked) {
                    selectedSizesValue = sizesRadios[i].value;
                    break;
                }
            }

            if (selectedSizesValue === null) {
                Swal.fire({ icon: 'error', title: 'اختار مقاس'})
                sizesRadios[0].focus();
                return;
            }
            if(selectedSizesValue === 'stand'){
                var sizesStander = document.getElementById('sizeStander');
                if(!sizesStander.value || sizesStander.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل المقاس فارغ'})
                    sizesStander.focus();
                    return
                }
            }
            if(selectedSizesValue === 'free'){
                var sizesFreeH = document.getElementById('height');
                var sizesFreeW = document.getElementById('width');
                var sizesFreeD = document.getElementById('depth');
                if(!sizesFreeH.value || sizesFreeH.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل الطول فارغ'})
                    sizesFreeH.focus();
                    return
                }
                if(!sizesFreeW.value || sizesFreeW.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل العرض فارغ'})
                    sizesFreeW.focus();
                    return
                }
                if(!sizesFreeD.value || sizesFreeD.value === ""){
                    Swal.fire({ icon: 'error', title: 'حقل الارتفاع فارغ'})
                    sizesFreeD.focus();
                    return
                }
            }
        }
            // alert('Item Send Success');

            const form = document.querySelector('#calculateform');
            const formData = new FormData(form);
            var xmlhttp = new XMLHttpRequest();

            // formData.forEach((value, key) => {
            //     console.log(key + ": " + value);  // عرض الاسم والقيمة لكل حقل
            // });

            resultShow.innerHTML = `
                <div class="alert alert-info" role="alert">
                    جاري تحليل البيانات...
                </div>
            `;
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {

                        const response = JSON.parse(this.responseText);
                        resultShow.innerHTML = response.content;
                        $('#footeraction').append(`
                            <a class="btn btn-success text-white" onclick="addToTable()"> {{ __('Add') }}</a>
                        `);

                    } else {
                        // في حالة حدوث خطأ في الاستجابة
                        resultShow.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                يوجد مشكلة، حاول مرة أخرى.
                            </div>
                        `;
                    }
                }
            };
            let url = "{{ route('manufacturing.calculate', ':id') }}";
            url = url.replace(':id', product_id);
            xmlhttp.open("POST", url, true);
            xmlhttp.send(formData);

            // alert(product_id);    // معرّف المنتج
        }


        function addToTable() {
            var name = $('input[name="name"]').val();
            var description = $('input[name="description"]').val();
            var price = $('input[name="price"]').val();
            var count = $('input[name="count"]').val();
            var design_note = $('textarea[name="design_note"]').val();

            var fileInput = document.getElementById('files');
            var file = fileInput.files[0];
            var formData = new FormData();
            if (file) {
                formData.append('file', file);
            }

            function createRow(imagepath = null) {
                var imageLink = imagepath
                    ? `<a target="__blank" class="btn btn-primary btn-sm" href="{{ url('storage/${imagepath}') }}">{{ __('view attachment') }}<i class="ti ti-eye"></i></a><input name="items[][attachment]" type="hidden" value="${imagepath}"> `
                    : '';

                var newRow = `
                    <tbody class="ui-sortable" data-repeater-item="">
                        <tr>
                            <td width="25%" class="form-group pt-0">
                                ${name}
                                <input name="items[][item]" type="hidden" value="${name}">
                                <input name="items[][type]" type="hidden" value="custom"><br>
                                ${imageLink}
                                <a href="#" class="btn btn-primary btn-sm" id="descriptionview">{{ __('view workorder') }}<i class="ti ti-link"></i></a>
                                <textarea class="form-control pro_description d-none" rows="1" placeholder="الوصف"  name="items[][description]">${description}</textarea>
                            </td>
                            <td>
                                <div class="form-group price-input input-group search-form">
                                    <input class="form-control quantity" required placeholder="الكمية" name="items[][quantity]" type="text" value="${count}">
                                    <span class="unit input-group-text bg-transparent"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group price-input input-group search-form">
                                    <input class="form-control price" required placeholder="السعر" name="items[][price]" type="text" value="${price}">
                                    <div class="invalid-feedback">يجب وضع اسم للمنتج</div>
                                    <span class="input-group-text bg-transparent">SR</span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group price-input input-group search-form">
                                    <input class="form-control discount" required placeholder="الخصم" name="items[][discount]" type="text" value="0">
                                    <span class="input-group-text bg-transparent">SR</span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="taxes"></div>
                                        <input class="form-control tax" name="items[][tax]" type="hidden" value="0">
                                        <input class="form-control itemTaxPrice" name="items[][itemTaxPrice]" type="hidden" value="0">
                                        <input class="form-control itemTaxRate" name="items[][itemTaxRate]" type="hidden" value="0">
                                    </div>
                                </div>
                            </td>
                            <td class="text-end amount">0.00</td>
                            <td>
                                <a href="#" class="ti ti-trash btn p-1 btn-danger text-white" data-repeater-delete=""></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <textarea class="form-control design_note" rows="1" placeholder="الوصف"  name="items[][design_note]">${design_note}</textarea>
                            </td>
                        </tr>
                    </tbody>`;
                $('table.table[data-repeater-list="items"]').append(newRow);
                Swal.fire({ icon: 'success', title: 'تمت إضافة منتج التصنيع' });
                $('#commonModal').modal('hide');
                $(".price").change();
                $(".discount").change();
                dataRepetorFix();
            }

    if (file) {
        // رفع الملف إذا كان موجودًا
        $.ajax({
            url: "{{ route('manufacturing.uploadfile') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.path) {
                    createRow(response.path);
                } else {
                    Swal.fire({ icon: 'error', title: 'حدث خطأ أثناء معالجة الصورة' });
                }
            },
            error: function(err) {
                Swal.fire({ icon: 'error', title: 'حدث خطأ أثناء رفع الملف' });
            }
        });
    } else {
        // إنشاء الصف مباشرة إذا لم يكن هناك ملف
        createRow();
    }


}


function dataRepetorFix(){
    var selector = "body";
        var itemCounter = 1;
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function () {
                    $(this).slideDown();
                    $(this).find('.item').attr('id', 'item' + itemCounter);
                    itemCounter++;
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                select2();
                },
                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                    }
                },
                ready: function (setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: false
            });

            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }
        }
}
    $(document).ready(function () {
        $('#descriptionview').on('click', function (e) {
            e.preventDefault(); // منع التوجيه الافتراضي للزر

            // الحصول على القيمة من الـ textarea
            let description = $('.pro_description').val();

            // التحقق إذا كانت الحقول فارغة
            if (!description) {
                alert('Please enter a description.');
                return;
            }

            // إرسال الطلب إلى الراوت باستخدام AJAX
            $.ajax({
                url: '{{ route("proposal.workorder") }}', // الراوت
                type: 'POST', // نوع الطلب
                data: {
                    description: description,
                    _token: '{{ csrf_token() }}' // لإضافة CSRF Token
                },
                success: function (response) {
                    // إعادة التوجيه إلى الصفحة الجديدة
                    window.location.href = response.redirect_url;
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });

</script>
