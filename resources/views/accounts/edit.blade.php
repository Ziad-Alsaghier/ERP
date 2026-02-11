<!-- Stylish Modal + jsTree -->
<style>
    .custom-select-wrapper {
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        padding: 1rem;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .jstree-anchor>i.fa {
        margin-right: 5px;
    }

    #jstree {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: .375rem;
        padding: 1rem;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.25rem;
    }

    .form-label {
        font-weight: 600;
    }

    .form-section {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
    }
</style>

<div class="modal fade" id="editModalDynamic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ __('Edit Account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{ Form::model($chartOfAccount, ['route' => ['account.update', $chartOfAccount->id], 'method' => 'PUT'])
                }}

                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('name', __('Account Name'), ['class' => 'form-label']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => true]) }}
                    </div>

                    <div class="form-group col-md-6">
                        {{ Form::label('Enabled', __('Enabled'), ['class' => 'form-label d-block']) }}
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="taxable" id="taxable" {{
                                $chartOfAccount->deleted_at ? '' : 'checked' }}>
                            <label class="form-check-label" for="taxable">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    {{ Form::label('parent_id', __('Select Parent Account'), ['class' => 'form-label']) }}

                    <input type="hidden" name="parent_id" id="parent_id" value="{{ $chartOfAccount->parent_id }}">
                    <input type="hidden" name="parent_chain" id="parent_chain">

                    <div class="custom-select-wrapper mt-2">
                        <input type="text" id="jstree-search" class="form-control mb-3"
                            placeholder="🔍 Search accounts...">
                        <div id="jstree" style="max-height: 300px; overflow-y: auto;"></div>
                    </div>
                </div>

                <div class="modal-footer mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                </div>

                {{ Form::close() }}
            </div>

        </div>
    </div>
</div>
@push('script-page')
<script>

    $(document).ready(function () {
        $('#editModalDynamic').on('shown.bs.modal', function () {
            const parentId = "{{ $chartOfAccount->parent_id }}";

            if ($.jstree.reference('#jstree')) {
                $('#jstree').jstree('destroy');
            }

            $('#jstree').jstree({
                core: {
                    data: @json($account_tree),
                    themes: {
                        icons: false,
                        responsive: true
                    }
                },
                plugins: ['search', 'wholerow', 'dnd', 'types'],
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

            // Enable search
            let to = false;
            $('#jstree-search').on('keyup', function () {
                if (to) clearTimeout(to);
                to = setTimeout(() => {
                    const val = $(this).val();
                    $('#jstree').jstree(true).search(val);
                }, 300);
            });

            // On selection
            $('#jstree').on('select_node.jstree', function (e, data) {
                const selectedId = data.node.id;
                const parentIds = $('#jstree').jstree(true).get_path(data.node, '/', true);
                $('#parent_id').val(selectedId);
                $('#parent_chain').val(JSON.stringify(parentIds));
            });

            // Auto-select existing parent node
            if (parentId) {
                $('#jstree').on('ready.jstree', function () {
                    $('#jstree').jstree(true).select_node(parentId);
                });
            }
        });
    });
</script>
