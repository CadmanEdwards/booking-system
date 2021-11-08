@extends('layouts.app')
@section('content')
    <h3 class="page-title">Settings</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.post.settings']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            Settings
        </div>

        <div class="panel-body">
            <div class="row">
            <div class="col-xs-6 form-group">
                {!! Form::label('dashboard_text', 'Dashboard Message', ['class' => 'control-label']) !!}
                {!! Form::textarea('dashboard_text',$settings->dashboard_text,['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id' => 'dashboard_text']) !!}
                <p class="help-block"></p>
                @if($errors->has('dashboard_text'))
                    <p class="help-block">
                        {{ $errors->first('dashboard_text') }}
                    </p>
                @endif
            </div>
            </div>

            <br>
            <hr>
            <br>
            <div class="row">
            <div class="col-xs-6 form-group" id="price_fee_container">
                <br>
                <button type="button" class="btn btn-primary" onclick="addMoreFields(this)">Add more</button>
                <input type="hidden" value="{{ count($price_fee) }}" id="countFields">
                <br>
                   @foreach($price_fee as $key => $value)
                    <div class="form-inline">
                    <div class="form-group" >
                        <label for="exampleInputName2">Price</label>
                        <input type="number" step="0.01" class="form-control" value="{{ $value->price }}" name="price_fee[{{ $key }}][price]"  placeholder="Price">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail2">Fee</label>
                        <input type="number" step="0.01" class="form-control" value="{{ $value->fee }}"  name="price_fee[{{ $key }}][fee]"  placeholder="Fee">
                    </div>
                        @if($key > 0)
                        <button type="button" class="btn btn-default" onclick="removeFields(this)">Remove</button>
                            @endif
                    </div>
                    @endforeach

            </div>
            </div>



        </div>

        {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}



        @section('javascript')
            @parent
            <script src="https://cdn.tiny.cloud/1/mahjowjx5cbjsjflu83u2suiolbmvkxa7e5ntqflqrx52tgt/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#dashboard_text',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak code',
            toolbar_mode: 'floating',
            height: 500
        });

        function addMoreFields(elements) {
            var i = $('#countFields').val();
            i++;
            $('#countFields').val(i)
            $('#price_fee_container').append(' <div class="form-inline">\n' +
                '                    <div class="form-group">\n' +
                '                        <label for="exampleInputName2">Price</label>\n' +
                '                        <input type="number" step="0.01" class="form-control" name="price_fee['+i+'][price]"  placeholder="Price">\n' +
                '                    </div>\n' +
                '                    <div class="form-group">\n' +
                '                        <label for="exampleInputEmail2">Fee</label>\n' +
                '                        <input type="number" step="0.01" class="form-control" name="price_fee['+i+'][fee]"  placeholder="Fee">\n' +
                '                    </div>\n' +
                '                        <button type="button" class="btn btn-default" onclick="removeFields(this)">Remove</button>  \n' +
                '                    </div>')

        }

        function removeFields(element) {
            $(element).parent().remove()
        }
    </script>
        @endsection

@endsection
