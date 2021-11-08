@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i>  Pages</h3>
    
    {!! Form::model($page, ['method' => 'PUT', 'route' => ['admin.pages.update', $page->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

         <div class="panel-body">
            <div class="row">
                
                    <div class="col-xs-12 form-group">
                            {!! Form::label('name', 'Subject *', ['class' => 'control-label']) !!}
                            {!! Form::text('page_subject', old('page_subject'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('page_subject'))
                                <p class="help-block">
                                    {{ $errors->first('page_subject') }}
                                </p>
                            @endif
                        </div>
                    <div class="col-xs-12 form-group">
                            {!! Form::label('name', 'Content *', ['class' => 'control-label']) !!}
                            {!! Form::textarea('page_content',old('page_content'),['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id'=> 'page_content']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('page_content'))
                                <p class="help-block">
                                    {{ $errors->first('page_content') }}
                                </p>
                            @endif
                    </div>     
                  
        </div>
    </div>
    
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
    <script>
         CKEDITOR.replace( 'page_content' );
        // $('.textarea').ckeditor(); // if class is prefered.
    </script>
    @stop