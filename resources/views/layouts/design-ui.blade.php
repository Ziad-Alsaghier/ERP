<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        <a href="{{route('design.show')}}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'design.show' ) ? ' active' : '' }}">{{__('SlideShow')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        <a href="{{route('design.show.category')}}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'design.show.category' ) ? ' active' : '' }}">{{__('Categories')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

    </div>
</div>
