@extends('layouts.admin')
@section('page-title')
    {{__('POS Vs Purchase')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('POS Vs Purchase')}}</li>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush
@push('script-page')
    <script>
        (function () {
            var chartBarOptions = {
                series: [
                    {
                        name: '{{ __("Profit") }}',
                        data:  {!! json_encode($profits) !!},

                    },
                ],

                chart: {
                    height: 300,
                    width: 1080,
                    type: 'area',
                    // type: 'line',
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
                    categories: {!! json_encode($monthList) !!},
                    title: {
                        text: '{{ __("Months") }}'
                    }
                },
                colors: ['#ffa21d', '#FF3A6E'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                yaxis: {
                    title: {
                        text: '{{ __("Profit") }}'
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#pos-vs-purchase"), chartBarOptions);
            arChart.render();
        })();
    </script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{$currentYear}}';
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
@endpush


@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}"
           data-original-title="{{__('Download')}}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('report.pos.vs.purchase'),'method' => 'GET','id'=>'pos_vs_purchase')) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row ">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('year', __('Year'),['class'=>'form-label'])}}
                                            {{ Form::select('year',$yearList,isset($_GET['year'])?$_GET['year']:'', array('class' => 'form-control select')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('pos_vs_purchase').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('report.pos.vs.purchase')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


    <div id="printableArea">
        <div class="row mt-0">
            <div class="col">
                <input type="hidden" value="{{__('POS Vs Purchase').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}" id="filename">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Report')}} :</h7>
                    <h6 class="report-text mb-0">{{__('POS Vs Purchase')}}</h6>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{__('Duration')}} :</h7>
                    <h6 class="report-text mb-0">{{$filter['startDateRange'].' to '.$filter['endDateRange']}}</h6>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12" id="chart-container">
                <div class="card">
                    <div class="card-body">
                        <div class="scrollbar-inner">
                            <div id="pos-vs-purchase" data-color="primary" data-height="300"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{__('Type')}}</th>
                                @foreach($monthList as $month)
                                    <th>{{$month}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{(__('POS'))}}</td>
                                @foreach($posTotal as $pos)
                                    <td>{{\Auth::user()->priceFormat($pos)}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>{{(__('Purchase'))}}</td>
                                @foreach($purchaseTotal as $purchase)
                                    <td>{{\Auth::user()->priceFormat($purchase)}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td colspan="13" class="text-dark"><span>{{__('Profit = POS - Purchase')}}</span></td>
                            </tr>
                            <tr>
                                <td><h6>{{(__('Profit'))}}</h6></td>

                                @foreach($profits as $profit)
                                    <td>{{\Auth::user()->priceFormat(str_replace(",", "", $profit))}}</td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


