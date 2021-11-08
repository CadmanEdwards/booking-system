@extends('layouts.auth')
@section('content')
 

    <div class="panel panel-default">
        <div class="panel-heading">
            Payment Response
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12 text-center marzingap divImg">
                      <img src="{{ url('/public/upload/'.$CmpDtl->cmp_logo1) }}">
                    </div>
                    <div class="col-md-12 text-center marzingap">
                      Wij hebben je betaling van factuur <b><strong>{{ $inv_numberStr }}</strong></b> ontvangen, Bedankt voor de snelle betaling, wij stellen dit zeer op prijs!
                    </div>  
                    <div class="col-md-12 text-center marzingap">
                      Met een hartelijke groet,
                    </div>
                    <div class="col-md-12 text-center marzingap">
                      {{ $CmpDtl->cmp_name }}<br>
                      {{ $CmpDtl->cmp_web }}<br>
                      {{ $CmpDtl->cmp_email }}
                    </div>  
                </div>
            </div>
        </div>
    </div>

<style type="text/css">
  .marzingap{margin:5px 0px }
  /*.divImg{height: 130px}*/
  .divImg img{max-height: 100%;max-width: 100%}
</style>
@stop
 
