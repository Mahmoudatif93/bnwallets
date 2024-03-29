@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.Companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.Companies.index') }}"> @lang('site.Companies')</a></li>
                <li class="active">@lang('site.edit')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.edit')</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.Companies.update', $category->id) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}

                 

                        <div class="form-group col-6"> <label>@lang('site.MainCompanies')</label>
                        <select name="main_company_id" id="main_company_id" class="form-control">
                            <option value="">@lang('site.MainCompanies')</option>
                            @foreach($mcategory as $MCompanie)
                            <option value="{{$MCompanie->id}}" {{ $category->main_company_id == $MCompanie->id ? 'selected' : '' }}>{{$MCompanie->name}}</option>
                            @endforeach
                        </select>
                    </div>

                            <div class="form-group">
                                <label>@lang('site.name')</label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}">
                            </div>

                            <div class="form-group">
                            <label for="images">@lang('site.image')</label>
                            <div class="input-group">
                                    <input type="file" name="company_image" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <img src="{{ $category->company_image}}" style="width: 100px" class="img-thumbnail image-preview" alt="">

                        </div>
                        
                        
                        
                         <div class="form-group">
                                <label>Acount ID</label>
                                <input type="text" name="AcountID" class="form-control" value="{{ $category->AcountID }}">
                            </div>
                            
                            
                                <div class="form-group">
                                <label>Acount Email</label>
                                <input type="text" name="AcountEmail" class="form-control" value="{{ $category->AcountEmail }}">
                            </div>
                            
                            
                                <div class="form-group">
                                <label>Acount Password</label>
                                <input type="password" name="AcountPassword" class="form-control" placeholder="******">
                            </div>



                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
