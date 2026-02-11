@extends('layouts.admin')
@section('page-title')
    {{ __('Chart of Accounts') }}
@endsection




@section('action-btn')
    <div class="float-end">
        @can('create chart of account')
            <a href="#" data-url="{{ route('account.create') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-size="lg" data-ajax-popup="true" data-title="{{ __('Create New Account') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <style>
        /* Green theme as before */
        #treeContainer {
            padding: 20px;
            border-radius: 10px;
        }

        .edit-icon {
            display: none;
            transition: all .5s;
        }

        li:hover .edit-icon {
            display: inline;

        }

        .jstree-anchor {
            font-weight: bold;
        }

        /* Plus/minus icons */
        .jstree-default .jstree-closed>.jstree-icon:before {
            content: "+";
            font-weight: bold;
            margin-right: 4px;
        }

        .jstree-default .jstree-open>.jstree-icon:before {
            content: "–";
            font-weight: bold;
            margin-right: 4px;
        }

        .jstree-default .jstree-icon.jstree-ocl {
            background: none !important;
        }

        .badge {
            font-size: 11px;
            margin-left: 5px;
        }
    </style>


    <div class="card d-none">

    </div>
    <div class="card" style="margin-top: 85px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>📊 {{ __('Chart of Accounts') }}</h4>
        </div>
        <div class="card-body" id="treeContainer">
            <div id="accountTree"></div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"> </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
<script>
        $(function () {
                'use strict';

                // Responsive Tree Container Padding
                function resizeTreeContainer() {
                    const padding = $(window).width() < 768 ? '20px' : '30px';
                    $('#treeContainer').css('padding', padding);
                }

                resizeTreeContainer();
                $(window).resize(resizeTreeContainer);

                // Initialize jsTree
             $('#accountTree').jstree({
    core: {
        check_callback: true,
        data: {
            url: function (node) {
                const baseUrl = '{{ route('tree.json') }}';
                return node.id === '#' ? baseUrl : `${baseUrl}?parentId=${node.id}`;
            },
            dataType: 'json'
        },
        themes: {
            responsive: true
        }
    },
    plugins: ['wholerow', 'types', 'state', 'dnd'],
    types: {
        account: {
            icon: 'fas fa-folder text-success'
        },
        transaction: {
            icon: 'fas fa-file text-warning'
        }
    }
});

                // Tree Events
                $('#accountTree').on('select_node.jstree', function (e, data) {
                    $('#accountTree').jstree('open_node', data.node);
                });

                $('#accountTree').on('loaded.jstree open_node.jstree refresh.jstree', function () {
                    appendEditIcons();
                });

                function appendEditIcons() {
                    $('#accountTree li').each(function () {
                        const $li = $(this);
                        const id = $li.attr('id');
                        const $a = $li.find('> a');

                        // Add Edit Icon if not present
                        if ($a.find('.edit-icon').length === 0) {
                            const editIcon = $(`
                                <a href='#' class='edit-icon ml-2' data-id='${id}' title='Edit'>
                                    <i class='fas fa-edit text-primary'></i>
                                </a>`);
                            $a.append(editIcon);
                        }
                        });
                }

                // Handle Edit icon click
                $(document).on('click', '.edit-icon', function (e) {
                    e.preventDefault();
                    const id = $(this).data('id');

                    axios.get('{{ route('account.edit') }}', {
                        params: { accountId: id }
                    })
                    .then(function (response) {
                        document.getElementById('editModalDynamic')?.remove();
                        document.body.insertAdjacentHTML('beforeend', response.data);
                        const modalEl = document.getElementById('editModalDynamic');
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();

                         $(document).ready(function () {
        $('#editModalDynamic').on('shown.bs.modal', function () {
            const parentId = "{{ $chartOfAccount->parent_id  ?? Null}}";

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
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
                });
                // Handle Delete icon click
                $(document).on('click', '.delete-icon', function (e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    if (confirm('هل أنت متأكد من حذف هذا الحساب؟')) {
                        axios.post('{{ route('account.delete') }}', {
                            accountId: id,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(function (response) {
                            $('#accountTree').jstree(true).refresh();
                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                    }
                });

                // Optional: Node move logic
                $('#accountTree').on('move_node.jstree', function (e, data) {
                    const movedId = data.node.id;
                    const newParentId = data.parent === '#' ? null : data.parent;
                    const newPosition = data.position;

                    $.ajax({
                        url: '{{ route('account.move') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: movedId,
                            parent_id: newParentId,
                            position: newPosition
                        },
                        success: function (res) {
                            console.log(res);
                            show_toastr('success', 'تم تحديث موقع الحساب بنجاح');
                        },
                        error: function (err) {
                            console.error(err);
                            show_toastr('error', 'حدث خطأ أثناء تحديث الحساب');
                        }
                    });
                });
            });
    </script>
@endsection
