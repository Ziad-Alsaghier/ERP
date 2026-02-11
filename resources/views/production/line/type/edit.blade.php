{{ Form::model($productionLineType, ['route' => ['production.line.type.update', $productionLineType->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
    <div class="modal-body">
        <div class="container">
            <!-- Success Alert -->
            <div id="alertSuccess" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                <span id="alertSuccessText"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Error Alert -->
            <div id="alertError" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                <ul id="alertErrorList" class="mb-0"></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <div class="card p-4 rounded-card shadow-sm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                            {{ Form::text('name', $productionLineType->name, ['class' => 'form-control', 'placeholder' => 'Enter Product Line Type', 'required']) }}
                            <div class="invalid-feedback">{{ __('Please Enter Product Line Type') }}.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Save') }}" class="btn btn-primary">
    </div>
{{ Form::close() }}
