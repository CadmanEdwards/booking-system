@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title"><i class="fa fa-user-circle ifont"></i> {{ $page->page_subject }}</h3>
 -->
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $page->page_subject }}
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class=" ">
                        
                        <tr>
                            <td>{!! $page->page_content !!}</td>
                        </tr>
                        
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            
        </div>
    </div>
@stop