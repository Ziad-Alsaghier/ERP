{{ Form::open(['route' => 'slideshow.store', 'enctype' => 'multipart/form-data']) }}

<style>
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .drag-drop-area {
        border: 2px dashed #6c757d;
        border-radius: .375rem;
        padding: 1.5rem;
        text-align: center;
        color: #6c757d;
        transition: border 0.3s ease;
        cursor: pointer;
        background-color: #f8f9fa;
    }

    .drag-drop-area.dragover {
        border-color: #007bff;
        background-color: #e9f5ff;
    }

    .image-preview {
        max-width: 100%;
        margin-top: 15px;
        max-height: 200px;
        display: none;
    }
</style>

<div class="modal-body">
    <div class="row">
        <!-- Name -->
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Slide Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => __('Enter slide name')]) }}
        </div>

        <!-- Title -->
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter slide title')]) }}
        </div>
        <div class="form-group col-md-6">
            <label for="lang" class="form-label">{{ __('Language') }}</label><span class="text-danger">*</span>
            <select name="lang" id="lang" class="form-control" required>
                <option value="">{{ __('Select language') }}</option>
                @foreach ($langs as $id => $lang)
                    <option value="{{ $lang->code }}">{{ strtoupper($lang->code) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Drag & Drop Image Upload -->
        <div class="form-group col-md-12 mt-3">
            {{ Form::label('image', __('Slide Image'), ['class' => 'form-label']) }}

            <div id="drop-area" class="drag-drop-area">
                Drag & drop an image here or click to browse
                {{ Form::file('image', ['id' => 'imageInput', 'accept' => 'image/*', 'style' => 'display:none']) }}
            </div>

            <img id="imagePreview" class="image-preview" />
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>

{{ Form::close() }}

<script>
    (function() {
        const dropArea = document.getElementById('drop-area');
        const imageInput = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');

        if (!dropArea || !imageInput || !preview) return;

        dropArea.addEventListener('click', () => imageInput.click());

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                imageInput.files = e.dataTransfer.files;
                showPreview(file);
            }
        });

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                showPreview(file);
            }
        });

        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    })();
</script>
