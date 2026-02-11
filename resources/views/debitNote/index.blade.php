@extends('layouts.admin')
@section('page-title')
{{__('Debit Note')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Debit Note')}}</li>
@endsection
@push('script-page')
<script>
let removedProducts = [];

function updateRemovedProductsInput() {
    $('#removedProducts').val(removedProducts.join(','));
    console.log(removedProducts);
}

$(document).on('change', '#bill', function () {
    var id = $(this).val();
    var url = "{{ route('bill.get') }}";

    $.ajax({
        url: url,
        type: 'get',
        cache: false,
        data: { 'bill_id': id },
        success: function(data) {
            $('#amount').val(0);  // Reset amount
            if (data) {
                console.log(data);
                // Handle products
                if (data.items && data.items.length) {
                    $('.products_bill').empty();

                    var productTable = `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    $.each(data.items, function(index, product) {
                        productTable += `
                            <tr>
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>${product.item_total}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm remove-product" data-id="${product.id}">Remove</button>
                                </td>
                            </tr>
                        `;
                    });

                    productTable += `</tbody></table>`;
                    $('.products_bill').append(productTable);
                }
            } else {
                alert("No data found for this bill.");
                $('.products_bill').empty();
            }
        },
        error: function(xhr, status, error) {
            let errMsg = xhr.responseJSON ? xhr.responseJSON.message : error;
            console.log("Error: " + errMsg);
            alert("An error occurred: " + errMsg);
        }
    });
});

// Handle removing products and updating amount
$(document).on('click', '.remove-product', function () {
    var productId = $(this).data('id');
    var productPrice = $(this).closest('tr').find('td:nth-child(3)').text(); 
    var currentAmount = parseFloat($('#amount').val());
    var priceToAdd = parseFloat(productPrice.replace(/[^0-9.-]+/g,""));

    var updatedAmount = currentAmount + priceToAdd;
    $('#amount').val(updatedAmount.toFixed(2));

    if (!removedProducts.includes(productId)) {
        removedProducts.push(productId);
    }

    updateRemovedProductsInput();

    $(this).replaceWith(`
        <button class="btn btn-success btn-sm undo-product" data-id="${productId}" data-price="${priceToAdd}">Undo</button>
    `);
});

// Undo for products
$(document).on('click', '.undo-product', function () {
    var productId = $(this).data('id');
    var productPrice = $(this).data('price');
    var currentAmount = parseFloat($('#amount').val());

    var updatedAmount = currentAmount - productPrice;
    $('#amount').val(updatedAmount.toFixed(2));

    removedProducts = removedProducts.filter(id => id !== productId);

    updateRemovedProductsInput();

    $(this).replaceWith(`
        <button class="btn btn-danger btn-sm remove-product" data-id="${productId}">Remove</button>
    `);
});
</script>




@endpush

@section('action-btn')
<div class="float-end">
    @can('create debit note')
    <a href="#" data-url="{{ route('bill.custom.debit.note') }}" data-ajax-popup="true" data-title="{{__('Create New Debit Note')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-plus"></i>
    </a>

    @endcan
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th> {{__('Bill')}}</th>
                                <th> {{__('Vendor')}}</th>
                                <th> {{__('Date')}}</th>
                                <th> {{__('Amount')}}</th>
                                <th> {{__('Description')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($bills as $bill)
                            @if(!empty($bill->debitNote))
                            @foreach ($bill->debitNote as $debitNote)

                            <tr class="font-style">
                                <td class="Id">
                                    <a href="{{ route('bill.show',\Crypt::encrypt($debitNote->bill)) }}" class="btn btn-outline-primary">{{ AUth::user()->billNumberFormat($bill->bill_id) }}

                                    </a>
                                </td>
                                <td>{{ (!empty($bill->vender)?$bill->vender->name:'-') }}</td>
                                <td>{{ Auth::user()->dateFormat($debitNote->date) }}</td>
                                <td>{{ Auth::user()->priceFormat($debitNote->amount) }}</td>
                                <td>{{!empty($debitNote->description)?$debitNote->description:'-'}}</td>
                                <td class="Action">
                                    <span>
                                        @can('edit debit note')
                                        <div class="action-btn bg-primary ms-2">
                                            <a data-url="{{ route('bill.edit.debit.note',[$debitNote->bill,$debitNote->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Debit Note')}}" href="#" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('edit debit note')
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => array('bill.delete.debit.note', $debitNote->bill,$debitNote->id),'id'=>'delete-form-'.$debitNote->id]) !!}

                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$debitNote->id}}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                        @endcan
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection