<aside class="main-sidebar">

<style>

.side-menu{margin-top: 21px;}
.side-menu span{
    font-weight: bold;font-size:20px;
}
.side-menu ul li a{
    font-weight: bold;font-size:16px;
}

.side-menu .slide.active .side-menu__label, .side-menu .slide.active .side-menu__icon {
    color: #b7b218;
}

.active {
  color: green;
}
</style>
    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('uploads/user_images/' .auth()->user()->image) }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{auth()->user()->first_name.' '.auth()->user()->last_name}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

    


        <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll" style="background-color:#000000;color: #8f8fab;width:240px">

        <ul class="sidebar-menu side-menu" data-widget="tree">

        <?php $routname=\Request::route()->getName();
            $activedas='';
            $activecomp='';
            $activeMaincomp='';
            $activecard='';
            $activeuser='';
 if(str_ireplace(".index","",$routname) == "dashboard") {$activedas='active';}
 else if($routname== 'dashboard.Companies.index') {$activecomp='active';
}
else if($routname== 'dashboard.Cards.index') {$activecard='active';
}
else if($routname== 'dashboard.users.index') {$activeuser='active';
}
else if($routname== 'dashboard.MainCompanies.index') {$activeMaincomp='active';
}

 else{$activedas='';$activecomp=''; $activecard='';$activeuser='';$activeMaincomp='';}?>

           <li class="slide nav-item <?= $activedas;?>"><a  href="{{ route('dashboard.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span 
           class="side-menu__label ">@lang('site.dashboard') 
   
          
        </span></a></li>


        @if (auth()->user()->hasPermission('read_MainCompanies'))
                <li class="slide nav-item <?= $activeMaincomp;?>"><a href="{{ route('dashboard.MainCompanies.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.MainCompanies')</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read_Companies'))
                <li class="slide nav-item <?= $activecomp;?>"><a href="{{ route('dashboard.Companies.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.Companies')</span></a></li>
            @endif
            @if (auth()->user()->hasPermission('read_Cards'))
                <li class="slide nav-item <?= $activecard;?> "><a href="{{ route('dashboard.Cards.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.Cards')</span></a></li>
            @endif

           
{{--
            @if (auth()->user()->hasPermission('read_clients'))
            <li class=" nav-item "><a href="{{ route('dashboard.clients.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.clients')</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item  "><a href="{{ route('dashboard.orders.index') }}"data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.orders')</span></a></li>
        @endif

        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.dubiorders.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.dubiorders')</span></a></li>
        @endif
        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.dubioff.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.dubioff')</span></a></li>
        @endif

        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.localcompany.index') }}" data-toggle="slide"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.localcompany')</span></a></li>
        @endif
        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.nationalcompany.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.nationalcompany')</span></a></li>
        @endif


        
        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.currancy.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.currancy')</span></a></li>
        @endif

        
        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item "><a href="{{ route('dashboard.currancylocal.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.currancyswagger')</span></a></li>
        @endif

        

        @if (auth()->user()->hasPermission('read_orders'))
            <li class="slide nav-item"><a href="{{ route('dashboard.swaggeroff.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.swagger')</span></a></li>
        @endif


        
        @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.swaggerorders.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.swaggerorders')</span></a></li>
            @endif
            
                  
        @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.AnisCodes.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">Anis Sold cards</span></a></li>
            @endif
            

    @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.products.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.products')</span></a></li>
            @endif
            
            
            @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.productsorders.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">Products Orders</span></a></li>
            @endif


    @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.ProfitRatio.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.profit_percent')</span></a></li>
            @endif
            

   @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item "><a href="{{ route('dashboard.Paymentcommissions.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.Paymentcommissions')</span></a></li>
            @endif
--}}
            
    @if (auth()->user()->hasPermission('read_users'))
            <li class="slide nav-item   <?=$activeuser?>"><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.users')</span></a></li>
            @endif


        







           {{-- @if (auth()->user()->hasPermission('read_users'))
                <li class="slide nav-item"><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-th"></i><span class="side-menu__label">@lang('site.users')</span></a></li>
            @endif--}}

            {{--<li class="slide nav-item"><a href="{{ route('dashboard.Companies.index') }}"><i class="fa fa-book"></i><span class="side-menu__label">@lang('site.Companies')</span></a></li>--}}
            {{----}}
            {{----}}
            {{--<li class="slide nav-item"><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-users"></i><span class="side-menu__label">@lang('site.users')</span></a></li>--}}

            {{--<li class="treeview">--}}
            {{--<a href="#">--}}
            {{--<i class="fa fa-pie-chart"></i>--}}
            {{--<span class="side-menu__label">الخرائط</span>--}}
            {{--<span class="pull-right-container">--}}
            {{--<i class="fa fa-angle-left pull-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="treeview-menu">--}}
            {{--<li class="slide nav-item">--}}
            {{--<a href="../charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a>--}}
            {{--</li>--}}
            {{--<li class="slide nav-item">--}}
            {{--<a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a>--}}
            {{--</li>--}}
            {{--<li class="slide nav-item">--}}
            {{--<a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a>--}}
            {{--</li>--}}
            {{--<li class="slide nav-item">--}}
            {{--<a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
        </ul>
        </aside>
    </section>

    <script>
document.querySelectorAll(".nav-item").forEach((ele) =>
  ele.addEventListener("click", function (event) {
   // event.preventDefault();
    document
      .querySelectorAll(".nav-item")
      .forEach((ele) => ele.classList.remove("active"));
    this.classList.add("active")
  })
);   
    </script>
</aside>

