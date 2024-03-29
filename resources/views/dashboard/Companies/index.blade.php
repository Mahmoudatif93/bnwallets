@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.Companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.Companies')</li>
            </ol>
        </section>



       <!-- <a href="{{ url('dashboard/checkpdf') }}"><i class="fa fa-dashboard"></i>print</a>-->

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.Companies') <small>{{ $Companies->total() }}</small></h3>

                    <form action="{{ route('dashboard.Companies.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search }}">
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasPermission('create_Companies'))
                                    <a href="{{ route('dashboard.Companies.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($Companies->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.MainCompanies')</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.kind')</th>
                                <th>@lang('site.image')</th>
                            <th>Acount ID</th>
                            <th>Acount Email</th>
                          
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($Companies as $index=>$category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->MainCompany->name }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                    @if($category->kind=="local")
                                    @lang('site.local')
                                    @elseif($category->kind=="national")
                                    @lang('site.national')
                                    @endif

                                    </td>
                                    
                                    <td><img src="{{$category->company_image}}" style="width: 100px"  class="img-thumbnail" alt="">
                                

                                </td>
 <td>{{ $category->AcountID }}</td>
  <td>{{ $category->AcountEmail }}</td>


                                                                   <td>
                                        @if (auth()->user()->hasPermission('update_Companies'))
                                            <a href="{{ route('dashboard.Companies.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                      
                                      {{--  @if (auth()->user()->hasPermission('delete_Companies'))
                                            <form action="{{ route('dashboard.Companies.destroy', $category->id) }}" method="post" style="display: inline-block">
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

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{ $Companies->appends(request()->query())->links() }}

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
