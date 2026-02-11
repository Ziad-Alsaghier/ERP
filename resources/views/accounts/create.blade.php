{{ Form::open(['route' => 'account.store']) }}

<style>
    .custom-select-wrapper {
        border: 1px solid #ced4da;
        border-radius: .375rem;
        padding: 0.5rem;
        background-color: white;
    }

    .jstree-anchor>i.fa {
        margin-right: 5px;
    }
</style>
<div class="modal-body">
    @if ($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
            data-url="{{ route('generate', ['chart of account']) }}" data-bs-placement="top"
            data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module --}}
    <div class="row">

        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required' , 'placeholder'=>__('Enter Name')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('parent_id', __('Select Parent'), ['class' => 'form-label']) }}

            {{-- Hidden Inputs --}}
            <input type="hidden" name="parent_id" id="parent_id">
            <input type="hidden" name="parent_chain" id="parent_chain">

            {{-- Simulated "select" dropdown using bootstrap --}}
            <div class="custom-select-wrapper">
                <input type="text" id="jstree-search" class="form-control mb-2" placeholder="{{ __('Search accounts...') }}">
                <div id="jstree" class="border p-2 rounded bg-white" style="max-height: 300px; overflow: auto;"></div>
            </div>
        </div>
       <div id="jstree"></div>
        <div class="col-md-2">
            <div class="form-group ">
                {{ Form::label('is_enabled', __('Is Enabled'), ['class' => 'form-label']) }}
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" name="is_enabled" checked id="is_enabled" >
                    <label class="custom-control-label form-check-label" for="is_enabled"></label>
                </div>
            </div>
        </div>



        <div class="form-group col-md-6 acc_type d-none">
            {{ Form::label('parent', __('Parent Account'), ['class' => 'form-label']) }}
            <select class="form-control select" name="parent" id="parent">
            </select>
        </div>

        {{-- <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2' , 'placeholder'=>__('Enter Description')]) !!}
        </div> --}}

    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}
<script>
    select2();
          $(function () {
        // Init jsTree
        $('#jstree').jstree({
            'core': {
                'data': @json($account_tree),
                'themes': {
                    'icons': false,
                    responsive: true
                }
            },
            'plugins': ['search', 'wholerow','dnd'],
             types: {
                        account: {
                            icon: 'fas fa-folder text-success'
                        },
                        transaction: {
                            icon: 'fas fa-money-bill-wave text-warning'
                        },
                        children: {
                            icon: 'fas fa-folder'
                        }
                    }
        });
        // Search plugin
        let to = false;
        $('#jstree-search').on('keyup', function () {
            if (to) clearTimeout(to);
            to = setTimeout(() => {
                let v = $(this).val();
                $('#jstree').jstree(true).search(v);
            }, 250);
        });

        // On select → store ID + parent chain
        $('#jstree').on('select_node.jstree', function (e, data) {
            const selectedId = data.node.id;
            const parentIds = $('#jstree').jstree(true).get_path(data.node, '/', true);
            $('#parent_id').val(selectedId);
            $('#parent_chain').val(JSON.stringify(parentIds));
        });
    });
    </script>
