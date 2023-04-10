@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.currancyswagger')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.currancyswagger')</li>
            </ol>
        </section>



       <!-- <a href="{{ url('dashboard/checkpdf') }}"><i class="fa fa-dashboard"></i>print</a>-->

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.currancyswagger') <small></small></h3>

               {{--     <form action="{{ route('dashboard.currancylocal.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search }}">
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create_Companiess'))
                                    <a href="{{ route('dashboard.currancylocal.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                   
                                @endif
                            </div>

                        </div>
                    </form>--}}
                    <!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($category->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نسبة ربح الكروت دولية انيس </th>
                               
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{{ 1 }}</td>
                                    <td>{{ $category->amount }} % </td>
                                 


                                                                   <td>
                                        @if (auth()->user()->hasPermission('update_Companies'))
                                            <a href="{{ route('dashboard.currancylocal.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                   
                                   {{--     @if (auth()->user()->hasPermission('delete_Companies'))
                                            <form action="{{ route('dashboard.currancylocal.destroy', $category->id) }}" method="post" style="display: inline-block">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif

                                        --}}
                                    </td>
                                </tr>

                         
                            </tbody>

                        </table><!-- end of table -->

              

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
