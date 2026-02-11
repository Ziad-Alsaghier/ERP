@extends('layouts.admin')

@section('page-title')
{{ __('Edit Asset Category') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('asset_categories.index') }}">{{ __('Asset Categories') }}</a></li>
<li class="breadcrumb-item">{{ __('Edit Asset Category') }}</li>
@endsection
@push('script-page')
<script>
    select2();
</script>
@endpush
@section('content')
<div class="row">
    {{ Form::model($assetCategory, ['url' => route('asset_categories.update', $assetCategory->id), 'method' => 'PUT', 'class' => 'w-100 needs-validation', 'novalidate']) }}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Arabic Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('arabic_name', __('Arabic Name'), ['class' => 'form-label']) }}
                            {{ Form::text('arabic_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Arabic Name'), 'required']) }}
                            <div class="invalid-feedback">{{ __('Please enter the Arabic name.') }}</div>
                        </div>
                    </div>
                    <!-- English Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('english_name', __('English Name'), ['class' => 'form-label']) }}
                            {{ Form::text('english_name', null, ['class' => 'form-control', 'placeholder' => __('Enter English Name'), 'required']) }}
                            <div class="invalid-feedback">{{ __('Please enter the English name.') }}</div>
                        </div>
                    </div>
                    <!-- Reference Number -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('reference_number', __('Reference Number'), ['class' => 'form-label']) }}
                            {{ Form::text('reference_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Reference Number'), 'required']) }}
                            <div class="invalid-feedback">{{ __('Please enter the reference number.') }}</div>
                        </div>
                    </div>
                    <!-- Is Depreciable -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('is_depreciable', __('Is Depreciable'), ['class' => 'form-label']) }}
                            <input type="checkbox" id="is_depreciable" name="is_depreciable" value="1"
                                {{ $assetCategory->is_depreciable ? 'checked' : '' }}>
                        </div>
                    </div>
                    <!-- Account -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('asset_account', __('Account'), ['class' => 'form-label']) }}
                            <select name="asset_account" id='asset_account' class="form-control select2" required>
                                <option value="" disabled>{{ __('--- Select Account ---') }}</option>
                                @foreach ($accounts_type as $type)
                                <optgroup label="{{ __($type['name']) }}">
                                    @foreach ($accounts as $chartAccount)
                                    @if ($type['id'] == $chartAccount['type'] && $type['name'] == 'Assets')
                                    <option value="{{ $chartAccount['id'] }}"
                                        {{ $chartAccount['id'] == $assetCategory->asset_account ? 'selected' : '' }}>
                                        {{ $chartAccount['code'] . ' - ' . __($chartAccount['name']) }}
                                    </option>
                                    @include('partials.sub-accounts', [
                                    'subAccounts' => $subAccounts,
                                    'parent_id' => $chartAccount['id'],
                                    'level' => 1
                                    ])
                                    @endif
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">{{ __('Please select an account.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Depreciation Fields -->
                <div id="depreciation_fields" class="col-12" style="display: {{ $assetCategory->is_depreciable ? 'block' : 'none' }};">
                    <div class="row">
                        <!-- Depreciation Method -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('depreciation_method', __('Depreciation Method'), ['class' => 'form-label']) }}
                                {{ Form::select('depreciation_method', [
                                    '' => __('Select Method'),
                                    'Straight-line method' => __('Straight-line method'),
                                    'Reducing balance method' => __('Reducing balance method'),
                                    'Units of production method' => __('Units of production method'),
                                    'Sum of years digits method' => __('Sum of years digits method')
                                ], null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <!-- Useful Life Unit -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('useful_life_unit', __('Useful Life Unit'), ['class' => 'form-label']) }}
                                {{ Form::select('useful_life_unit', [
                                    '' => __('Select Unit'),
                                    'years' => __('Years'),
                                    'percent' => __('Percent')
                                ], null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <!-- Useful Life -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('useful_life', __('Useful Life'), ['class' => 'form-label']) }}
                                {{ Form::number('useful_life', null, ['class' => 'form-control', 'placeholder' => __('Enter Useful Life')]) }}
                            </div>
                        </div>
                        <!-- Depreciation Rate -->
                        <!-- Depreciation Expense Account -->
                        <div class="form-group col-md-6">
                            <div class="btn-box">
                                {{ Form::label('depreciation_expense_account', __('Depreciation Account'), ['class' => 'form-label']) }}
                                <select name="depreciation_expense_account" class="form-control select2" id="depreciation_expense_account" style="direction: ltr; text-align:left;">
                                    <option value="" class="ms-5">{{ __(' --- Select Account ---') }}</option>
                                    @foreach ($accounts_type as $type)
                                    <optgroup label="{{ __($type['name']) }}">
                                        @foreach ($accounts as $chartAccount)
                                        @if ($type['id'] == $chartAccount['type'] && $type['name'] == 'Expenses')
                                        <option value="{{ $chartAccount['id'] }}" class="subAccount"
                                            {{ $chartAccount['id'] == $assetCategory->depreciation_expense_account ? 'selected' : '' }}>
                                            {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                        </option>
                                        @include('partials.sub-accounts', [
                                        'subAccounts' => $subAccounts,
                                        'parent_id' => $chartAccount['id'],
                                        'level' => 1
                                        ])
                                        @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Accumulated Depreciation Account -->
                        <div class="form-group col-md-6">
                            <div class="btn-box">
                                {{ Form::label('accumulated_depreciation_account', __('Accumulated Depreciation Account'), ['class' => 'form-label']) }}
                                <select name="accumulated_depreciation_account" class="form-control select2" id="accumulated_depreciation_account" style="direction: ltr; text-align:left;">
                                    <option value="" class="ms-5">{{ __(' --- Select Account ---') }}</option>
                                    @foreach ($accounts_type as $type)
                                    <optgroup label="{{ __($type['name']) }}">
                                        @foreach ($accounts as $chartAccount)
                                        @if ($type['id'] == $chartAccount['type'] && $type['name'] == 'Liability')
                                        <option value="{{ $chartAccount['id'] }}"
                                            {{ $chartAccount['id'] == $assetCategory->accumulated_depreciation_account ? 'selected' : '' }}>
                                            {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                        </option>
                                        @include('partials.sub-accounts', [
                                        'subAccounts' => $subAccounts,
                                        'parent_id' => $chartAccount['id'],
                                        'level' => 1
                                        ])
                                        @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Manual Depreciation -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('manual_depreciation', __('Manual Depreciation'), ['class' => 'form-label']) }}
                                <input type="checkbox" value="1" id="manual_depreciation" name="manual_depreciation"
                                    {{ $assetCategory->manual_depreciation ? 'checked' : '' }} />
                            </div>
                        </div>

                        <!-- Recorded Depreciation -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('recorded_depreciation', __('Recorded Depreciation'), ['class' => 'form-label']) }}
                                <input type="checkbox" value="1" id="recorded_depreciation" name="recorded_depreciation"
                                    {{ $assetCategory->recorded_depreciation ? 'checked' : '' }} />
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" onclick="location.href='{{ route('asset_categories.index') }}'">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        </div>
    </div>
    {{ Form::close() }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDepreciableCheckbox = document.getElementById('is_depreciable');
        const depreciationFields = document.getElementById('depreciation_fields');

        // Toggle depreciation fields visibility
        isDepreciableCheckbox.addEventListener('change', function() {
            if (this.checked) {
                depreciationFields.style.display = 'block';
            } else {
                depreciationFields.style.display = 'none';
            }
        });
    });
</script>
@endsection