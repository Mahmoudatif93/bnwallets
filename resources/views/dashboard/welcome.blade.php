@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dashboard')</h1>

            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</li>
            </ol>
        </section>

        <style>
                #div1 {
                    width: 400px;
                    height: 38px;
                    text-align: center;
                    background-color: #e6eef1;
                    font-weight: bold;
                    position: relative;
                    font-size: 20px;
                    animation: mymove 7s infinite;
                }

                #div1 {
                    animation-timing-function: linear;
                }


                @keyframes mymove {
                    from {
                        left: 0px;
                    }

                    to {
                        left: 300px;
                    }
                }
            </style>
        <section class="content">
        <div id="div1">Hi     @if(!empty(Auth::user())) {{ Auth::user()->first_name .' ' . Auth::user()->last_name }} @endif
            <div class="row">
<!--
                {{-- categories--}}
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{-- $companies --}}</h3>

                            <p>@lang('site.Companies')</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('dashboard.Companies.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>


                {{--clients--}}
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{-- $cards --}}</h3>

                            <p>@lang('site.Cards')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <a href="{{ route('dashboard.Cards.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>



                {{--users--}}
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h3>{{-- $clients --}}</h3>

                            <p>@lang('site.clients')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route('dashboard.clients.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>



<br>
                {{--products--}}
                <div class="col-lg-4 col-xs-6 mt-5">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{-- $orders --}}</h3>

                            <p>@lang('site.orders')</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('dashboard.orders.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>


                <div class="col-lg-4 col-xs-6 mt-5">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{-- $dubiorders --}}</h3>

                            <p>@lang('site.dubiorders')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route('dashboard.dubiorders.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>


                <div class="col-lg-4 col-xs-6 mt-5">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>
                              {{--  @if(!empty($alldata['results']))
                                {{ count($alldata['results']) }}
                                @else 
                               {{ "0"}}
                                @endif --}}
                                </h3>

                            <p>@lang('site.swaggerorders')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route('dashboard.swaggerorders.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>



                <div class="col-lg-12 col-xs-12 mt-5">
                    <div class="small-box " style="background-color: #7d8c95;">
                        <div class="inner">
                            <h3>{{-- $profit --}}</h3>
                            <h3>@lang('site.profit_percent')</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>

            

                
                              -->
            </div>
            
            <h1>Reports Analyticals Soon</h1>
            <!-- end of row -->

       

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection

