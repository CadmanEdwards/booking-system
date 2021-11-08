<script type="text/javascript">
	var BASE_URL = "{{ url('') }}";
</script>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="{{ asset('public/js') }}/pdfmake.min.js"></script>
<script src="{{ asset('public/js') }}/vfs_fonts.js"></script>
<script src="{{ asset('public/js') }}/buttons.html5.min.js"></script>
<script src="{{ asset('public/js') }}/buttons.print.min.js"></script>
<script src="{{ asset('public/js') }}/buttons.colVis.min.js"></script>
<script src="{{ asset('public/js') }}/dataTables.select.min.js"></script>
<script src="{{ asset('public/js') }}/jquery-ui.min.js"></script>
<script src="{{ asset('quickadmin/js') }}/bootstrap.min.js"></script>
<script src="{{ asset('quickadmin/js') }}/select2.full.min.js"></script>
<script src="{{ asset('quickadmin/js') }}/main.js?{{ time() }}"></script>

<script>
    window._token = '{{ csrf_token() }}';
     /*$(document).ready(function() {
            $(".hibutton").click(function(){
               alert("jjhkvbnfdkbhdjk nb"); 
            });
     });*/
</script>



@yield('javascript')