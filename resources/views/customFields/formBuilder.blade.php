@if($customFields)
    @foreach($customFields as $customField)
        @if($customField->type == 'text')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }} @if ($customField->required == 1 )<span class="text-danger">*</span>@endif
                <div class="input-group">
                    @if ($customField->required == 1 )
                    {{ Form::text('customField['.$customField->id.']', null, array('class' => 'form-control' , 'required' => 'required')) }}
                    @else
                    {{ Form::text('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                    @endif
                </div>
            </div>
        @elseif($customField->type == 'email')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }} @if ($customField->required == 1 )<span class="text-danger">*</span>@endif
                <div class="input-group">
                    @if ($customField->required == 1 )
                    {{ Form::email('customField['.$customField->id.']', null, array('class' => 'form-control','required' => 'required')) }}
                    @else
                    {{ Form::email('customField['.$customField->id.']', null, array('class' => 'form-control')) }}

                    @endif

                </div>
            </div>
        @elseif($customField->type == 'number')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }} @if ($customField->required == 1 )<span class="text-danger">*</span>@endif
                <div class="input-group">
                    @if ($customField->required == 1 )
                    {{ Form::number('customField['.$customField->id.']', null, array('class' => 'form-control','required' => 'required')) }}
                    @else
                    {{ Form::number('customField['.$customField->id.']', null, array('class' => 'form-control')) }}

                    @endif

                </div>
            </div>
        @elseif($customField->type == 'date')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }} @if ($customField->required == 1 )<span class="text-danger">*</span>@endif
                <div class="input-group">
                    @if ($customField->required == 1 )
                    {{ Form::date('customField['.$customField->id.']', null, array('class' => 'form-control','required' => 'required')) }}
                    @else
                    {{ Form::date('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                    @endif

                </div>
            </div>
        @elseif($customField->type == 'textarea')
            <div class="form-group">
                {{ Form::label('customField-'.$customField->id, __($customField->name),['class'=>'form-label']) }} @if ($customField->required == 1 )<span class="text-danger">*</span>@endif
                <div class="input-group">
                    @if ($customField->required == 1 )
                    {{ Form::textarea('customField['.$customField->id.']', null, array('class' => 'form-control','required' => 'required')) }}
                    @else
                    {{ Form::textarea('customField['.$customField->id.']', null, array('class' => 'form-control')) }}
                    @endif


                </div>
            </div>
        @endif
    @endforeach
@endif


