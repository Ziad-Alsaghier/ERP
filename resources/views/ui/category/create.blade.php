
<style>
    /* Floating Labels */
.animated-group {
    position: relative;
    margin-bottom: 1.5rem;
}
.animated-label {
    position: absolute;
    top: -8px;
    left: 15px;
    background: #fff;
    padding: 0 5px;
    font-size: 13px;
    color: #007bff;
    transition: 0.3s ease-in-out;
    z-index: 1;
}

/* Custom Select Styling */
.custom-select {
    border-radius: 10px;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    background-color: #fff;
    transition: border-color 0.3s;
}
.custom-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Switch Styling */
.switch {
    position: relative;
    display: inline-block;
    width: 55px;
    height: 28px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0;
    right: 0; bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px; width: 20px;
    left: 4px; bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #28a745;
}
input:checked + .slider:before {
    transform: translateX(26px);
}
.switch-label {
    margin-left: 12px;
    font-weight: 600;
    vertical-align: middle;
}

/* Animated Buttons */
.btn-animated {
    transition: all 0.3s ease;
}
.btn-animated:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

</style>
{{ Form::open(array('url' =>route('design.category.store'),'enctype' => 'multipart/form-data', 'class' => 'w-100 needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row px-2">
        {{-- Category Selector --}}
        <div class="form-group col-md-12 animated-group">
            <label class="animated-label" for="category_id">{{ __('Select Category') }}</label>
            {{ Form::select('cat_id', $categories, null, ['class' => 'form-control custom-select', 'id' => 'category_id', 'required']) }}
        </div>

        {{-- Enabled Switch --}}
        <div class="form-group col-md-6">
            <label class="d-block mb-2">{{ __('Status') }}</label>
            <label class="switch">
                <input type="checkbox" name="is_enabled" value="1" checked>
                <span class="slider round"></span>
                <span class="switch-label">{{ __('Enabled') }}</span>
            </label>
        </div>

        {{-- Header/Footer Selector --}}
        <div class="form-group col-md-6 animated-group">
            <label class="animated-label" for="position">{{ __('Show Form In') }}</label>
            {{ Form::select('section', ['header' => 'Header', 'footer' => 'Footer'], null, ['class' => 'form-control custom-select', 'id' => 'position', 'required']) }}
        </div>

    </div>
</div>

<div class="modal-footer px-2">
    <button type="button" class="btn btn-light btn-animated" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary btn-animated">{{ __('Save') }}</button>
</div>
{{ Form::close() }}
