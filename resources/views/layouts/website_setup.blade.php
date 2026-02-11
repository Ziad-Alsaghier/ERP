<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        <a href="{{route('shipping.index')}}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'shipping.index' ) ? ' active' : '' }}">{{__('shipping')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
    </div>
</div>
