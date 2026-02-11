@extends('layouts.admin')
@section('page-title')
{{ __('Edit Asset') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('fixed-assets.index') }}">{{ __('Assets') }}</a></li>
<li class="breadcrumb-item">{{ __('Edit Asset') }}</li>
@endsection

@section('content')
<div class="row">
    {{ Form::model($asset, ['url' => route('fixed-assets.update', $asset->id), 'method' => 'PUT', 'class' => 'w-100 needs-validation', 'novalidate', 'enctype' => 'multipart/form-data']) }}
    <div class="col-12">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('arabic_name', __('Arabic Name'), ['class' => 'form-label']) }}
                            {{ Form::text('arabic_name', $asset->arabic_name, ['class' => 'form-control', 'placeholder' => __('Enter Arabic Name'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the Arabic name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('english_name', __('English Name'), ['class' => 'form-label']) }}
                            {{ Form::text('english_name', $asset->english_name, ['class' => 'form-control', 'placeholder' => __('Enter English Name'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the English name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('reference_number', __('Reference Number'), ['class' => 'form-label']) }}
                            {{ Form::text('reference_number', $asset->reference_number, ['class' => 'form-control', 'placeholder' => __('Enter Reference Number'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the reference number.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                            {{ Form::select('category', $category, $asset->category, ['class' => 'form-control select2', 'placeholder' => __('Choice Category'), 'required' => true]) }}
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description', $asset->description, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('measurement_unit', __('Measurement Unit'), ['class' => 'form-label']) }}
                            {{ Form::select('measurement_unit', $unit, $asset->measurement_unit, ['class' => 'form-control', 'id' => 'measurement_unit', 'placeholder' => __('Enter Measurement Unit'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the measurement unit.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('tax_percentage', __('Tax Percentage'), ['class' => 'form-label']) }}
                            {{ Form::select('tax_percentage', $tax, $asset->tax_percentage, ['class' => 'form-control', 'id' => 'tax_percentage', 'placeholder' => __('Enter Tax Percentage'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the tax percentage.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('barcode', __('Barcode'), ['class' => 'form-label']) }}
                            {{ Form::text('barcode', $asset->barcode, ['class' => 'form-control', 'placeholder' => __('Enter Barcode')]) }}
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        {{Form::label('asset_image',__('Asset Image'),['class'=>'form-label'])}}
                        <div class="choose-file">
                            <label for="asset_image" class="form-label">
                                <input type="file" class="form-control" name="asset_image" id="asset_image" data-filename="asset_image_edit">
                                @if($asset->asset_image)
                                <img id="image" class="mt-3" src="{{asset(Storage::url('uploads/pro_image/'.$asset->asset_image)) }}" style="width:25%;" />
                                @else
                                <img id="image" class="mt-3" style="width:25%;" />
                                @endif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" onclick="location.href = '{{ route('fixed-assets.index') }}';">{{ __('Cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
    </div>
    {{ Form::close() }}
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('asset_image').onchange = function() {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image').src = src
        }
    })
</script>
