@extends('layouts.admin')
@section('page-title')
    {{__('Warehouse Report')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{ __('Warehouse Report') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            $.ajax({
                url: '/userinfo/img',
                method: 'GET',
                success: function(response) {
                    var logo_image = '<img src="' + response.logo + '" alt="Company Logo" >';
                    var settings_data = response.settings;
                    var printableArea = $('#printableArea');
                    var topHeader = $('<div class="top-header row" style="direction: ltr"></div>');
                    topHeader.append('<div class="col" style="width: 100%;max-width: 115px;">' + settings_data + '</div>');
                    topHeader.append('<div class="col" style="text-align: right;">' + logo_image + '</div>');
                    printableArea.prepend(topHeader);
                    var printContents = document.getElementById('printableArea').innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    $('.card').removeClass();
                    $('.table-responsive').removeClass();
                    setTimeout(() => {
                        window.print();
                        document.body.innerHTML = originalContents;
                        $('.top-header').empty();
                        location.reload();
                    }, "1000");
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log the error to the console
                    alert('Failed to fetch image data.');
                }
            });
        }
    </script>

    <script>
        (function () {
            var chartBarOptions = {
                series: [
                    {
                        name: '{{ __("Product") }}',
                        data:  {!! json_encode($warehouseProductData) !!},
                        // data:  [150,90,160,80],
                    },
                ],

                chart: {
                    height: 300,
                    width: 1080,
                    type: 'area',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($warehousename) !!},
                    title: {
                        text: '{{ __("Warehouse") }}'
                    }
                },
                colors: ['#6fd944'],


                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },

                {{--yaxis: {--}}
                {{--    title: {--}}
                {{--        text: '{{ __("Product") }}'--}}
                {{--    },--}}

                {{--}--}}

            };
            var arChart = new ApexCharts(document.querySelector("#warehouse_report"), chartBarOptions);
            arChart.render();
        })();

    </script>

@endpush
@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}" data-original-title="{{__('Download')}}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a>
    </div>
@endsection

@section('content')

    <div id="printableArea">
        <div class="row mt-3">
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Report')}} :</h7>
                    <h6 class="report-text mb-0">{{__('Warehouse Report')}}</h6>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Total Warehouse')}} :</h7>
                    <h6 class="report-text mb-0">{{$totalWarehouse}}</h6>
                </div>
            </div>

            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Total Product')}} :</h7>
                    <h6 class="report-text mb-0">{{$totalProduct}}</h6>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row ">
                            <div class="col-6">
                                <h6>{{ __('Warehouse Report') }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="warehouse_report"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



