@extends('layouts.auth')

@section('content')
    <div class="row">
        <div class="loginouter">
            <div class="panel panel-default">
                <div class="panel-heading bold">{{ ucfirst(config('app.name')) }} Login</div>
                <div class="panel-body">
                    
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were problems with input:
                            <br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form class="form-horizontal"
                          role="form"
                          method="POST"
                          action="{{ url('login') }}">
                        <input type="hidden"
                               name="_token"
                               value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Email</label>

                            <div class="col-md-6">
                                <input type="email"
                                       class="form-control"
                                       name="email"
                                       value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password"
                                       class="form-control"
                                       name="password">
                            </div>
                        </div>
                      

                      @if(session()->get('login.attempts') > 3) 
                        <div class="row">
                          <div class="col-md-4"></div>
                          <div class="form-group col-md-4">
                             <div class="captcha">
                               <span>{!! captcha_img() !!}</span>
                               <button type="button" class="btn btn-success"><i class="fa fa-refresh" id="refresh"></i></button>
                               </div>
                            </div>
                        </div>
                         <div class="row">
                          <div class="col-md-4"></div>
                            <div class="form-group col-md-4">
                             <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha" autocomplete="off"></div>
                          </div>
                       @endif   
						<div class="form-group">
                            <div class="col-md-8" style="padding-left: 80px;">
                               <div>
                                <a href="{{ route('auth.password.reset') }}">Forgot your password?</a>
                            	</div>
                            	 <div>
                                <label>
                                    <input type="checkbox"
                                           name="remember"> Remember me
                                </label>
                            </div>
                            </div>
                            <div class="col-md-4 fright">
                                <button type="submit"
                                        class="btn btn-primary"
                                        style="margin-right: 15px;">
                                    Login
                                </button>
                            </div>
                        </div>
                        
 
                    </form>
                </div>
            </div>
        </div>
    </div>
 @section('javascript')
 <script type="text/javascript">
$('#refresh').on('click',function(){
  $.ajax({
     type:'GET',
     url:'refreshcaptcha',
     success:function(data){
        $(".captcha span").html(data.captcha);
     }
  });
});
</script>
@stop
@endsection
  