<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

@if(Auth::user()->organisations()->count() > 1 || Auth::user()->hasRole('admin'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> Institutions</a></li>
@elseif(Auth::user()->organisations()->count() === 1)
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation/'.Auth::user()->organisations->first()?->id).'/show' }}"><i class="la la-home nav-icon"></i> Institutions: {{ Auth::user()->organisations->first()->name }}</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation/'.Auth::user()->organisations->first()?->id).'/portfolio' }}"><i class="la la-chart-bar nav-icon"></i> Review Portfolio</a></li>
@endif
<hr/>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('project') }}'><i class='nav-icon la la-question'></i> Projects</a></li>


@if(Auth::user()->hasRole('admin'))

    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('red-line') }}'><i class='nav-icon la la-question'></i> Red lines</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('principle') }}"><i class="nav-icon la la-question"></i> Principles</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('score-tag') }}"><i class="nav-icon la la-question"></i> Score tags</a></li>
    <hr/>

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-question"></i> Users</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role-invite') }}"><i class="nav-icon la la-question"></i> Admin User Invites</a></li>
    <hr/>

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('continent') }}"><i class="nav-icon la la-question"></i> Continents</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('region') }}"><i class="nav-icon la la-question"></i> Regions</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('country') }}"><i class="nav-icon la la-question"></i> Countries</a></li>
@endif


