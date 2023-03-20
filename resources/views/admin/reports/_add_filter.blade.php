<div class="form-group m-form__group row">
    {!! Form::label(($type == 5 || $type == 6 || $type == 1 || $type == 3 || $type == 2)?$title.'*':$title,
    null,array('class'=>'col-form-label
    col-lg-2 col-sm-12'))
    !!}
    <div class="col-lg-6 col-md-9 col-sm-12">
        <div class="input-group">
            {!!Form::select($name,@$list,[],['class'=>'form-control ','id'=>$name,'multiple'=>false 
            ,'data-none-selected-text' => __('formname.select_type',['type'=>$title]) ])!!}
        </div>
        <span class="dynamicError">
            @if($errors->has($name))
            <p class="errors">{{$errors->first($name)}}</p>
            @endif
        </span>
        <span class="m-form__help"></span>
    </div>
</div>
@if($type == 6)
<div class="form-group m-form__group row">
    {!! Form::label('Machine',
    null,array('class'=>'col-form-label
    col-lg-2 col-sm-12'))
    !!}
    <div class="col-lg-6 col-md-9 col-sm-12">
        <div class="input-group">
            {!!Form::select('machine_id',[],[],['class'=>'form-control ','id'=>'machine_id','multiple'=>false 
            ,'data-none-selected-text' => __('formname.select_type',['type'=>'Machine']) ])!!}
        </div>
        <span class="machineError">
            @if($errors->has('machine_id'))
            <p class="errors">{{$errors->first('machine_id')}}</p>
            @endif
        </span>
        <span class="m-form__help"></span>
    </div>
</div>
@endif
