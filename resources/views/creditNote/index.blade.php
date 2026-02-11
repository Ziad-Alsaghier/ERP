@extends('layouts.admin')
@section('page-title')
    {{__('Manage Credit Notes')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Credit Note')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create credit note')
            <a data-url="{{ route('invoice.custom.credit.note') }}" data-ajax-popup="true" data-title="{{__('Manage Credit Notes Dashboard')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@push('script-page')
    
    <script>
    // edited muhammed 9/26
    let removedProducts = [];

function updateRemovedProductsInput() {
    $('#removedProducts').val(removedProducts.join(','));
    console.log(removedProducts)
}

$(document).on('change', '#invoice', function () {
    var id = $(this).val();
    var url = "{{ route('invoice.get') }}";

    $.ajax({
        url: url,
        type: 'get',
        cache: false,
        data: { 'id': id },
        success: function(data) {
            $('#amount').val(0)
            if (data && data.invoice && data.invoice.length) {
               
                $('.products_invoice').empty();

                var table = `
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

                $.each(data.invoice, function(index, product) {
                    
                    table += `
                        <tr>
                            <td>${product?.name ?? 'N/A'}</td>
                            <td>${product.quantity}</td>
                            <td>${product.price}</td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-product" data-id="${product?.id}">Remove</button>
                            </td>
                        </tr>
                    `;
                });
                table += `</tbody></table>`;
                $('.products_invoice').append(table);
            } else {
                alert("No products found for this invoice.");
                $('.products_invoice').empty();
            }
        },
        error: function(xhr, status, error) {
            let errMsg = xhr.responseJSON ? xhr.responseJSON.message : error;
            console.log("Error: " + errMsg);
            alert("An error occurred: " + errMsg);
        }
    });
});

$(document).on('click', '.remove-product', function () {
    
    var productId = $(this).data('id');
    var productPrice = $(this).closest('tr').find('td:nth-child(3)').text(); // Get product price from row
    var currentAmount = parseFloat($('#amount').val()); // Get current amount
    var priceToAdd = parseFloat(productPrice.replace(/[^0-9.-]+/g,"")); // Convert price to number

    // Add the product price to the current amount
    var updatedAmount = currentAmount + priceToAdd;
    console.log({productId , productPrice , currentAmount , priceToAdd  , updatedAmount})
    $('#amount').val(updatedAmount.toFixed(2)); // Update amount field

    // Add the product ID to the removedProducts array if not already added
    if (!removedProducts.includes(productId)) {
        removedProducts.push(productId);
    }

    // Update the hidden input with the list of removed products
    updateRemovedProductsInput();

    // Change the button to "Undo" instead of removing the row
    $(this).replaceWith(`
        <button class="btn btn-success btn-sm undo-product" data-id="${productId}" data-price="${priceToAdd}">Undo</button>
    `);
});
$(document).on('click', '.undo-product', function () {
    var productId = $(this).data('id');
    var productPrice = $(this).data('price');
    var currentAmount = parseFloat($('#amount').val()); // Get current amount

    // Subtract the product price from the current amount
    var updatedAmount = currentAmount - productPrice;
    $('#amount').val(updatedAmount.toFixed(2)); // Update amount field

    // Remove the product ID from the removedProducts array
    removedProducts = removedProducts.filter(id => id !== productId);

    // Update the hidden input with the new list of removed products
    updateRemovedProductsInput();

    // Change the button back to "Remove"
    $(this).replaceWith(`
        <button class="btn btn-danger btn-sm remove-product" data-id="${productId}">Remove</button>
    `);
});
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style mt-2">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{__('Invoice')}}</th>
                                <th> {{__('Customer')}}</th>
                                <th> {{__('Date')}}</th>
                                <th> {{__('Amount')}}</th>
                                <th> {{__('Description')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                @if(!empty($invoice->creditNote))
                                @foreach ($invoice->creditNote as $creditNote)
                                        <tr>
                                            <td class="Id">
                                                
                                                <a href="{{ route('creditNote.show',\Crypt::encrypt($creditNote->invoice)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            </td>
                                            <td>{{ (!empty($invoice->customer)?$invoice->customer->name:'-') }}</td>
                                            <td>{{ Auth::user()->dateFormat($creditNote->date) }}</td>
                                            <td>{{ Auth::user()->priceFormat($creditNote->amount) }}</td>
                                            <td>{{!empty($creditNote->description)?$creditNote->description:'-'}}</td>
                                            <td>
                                                @can('edit credit note')
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a data-url="{{ route('invoice.edit.credit.note',[$creditNote->id,$creditNote->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Credit Note')}}" href="#" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('edit credit note')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => array('invoice.delete.credit.note', $creditNote->id,$creditNote->id),'class'=>'delete-form-btn','id'=>'delete-form-'.$creditNote->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                @endcan
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
