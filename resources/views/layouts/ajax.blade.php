<div class="row">
    <div class="col-md-12">

        @if (Session::has('message'))
            <div class="note note-info">
                <p>{{ Session::get('message') }}</p>
            </div>
        @endif
        @if ($errors->count() > 0)
            <div class="note note-danger">
                <ul class="list-unstyled">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </div>
</div>
  @include('partials.javascripts')