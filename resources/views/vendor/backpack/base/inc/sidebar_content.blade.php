<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

@if(Auth::user()->organisations()->count() > 1 || Auth::user()->hasRole('Site Admin'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> Institutions</a></li>
@elseif(Auth::user()->organisations()->count() === 1)
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation/'.Auth::user()->organisations->first()?->id).'/show' }}"><i class="la la-home nav-icon"></i> Institutions: {{ Auth::user()->organisations->first()->name }}</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('organisation/'.Auth::user()->organisations->first()?->id).'/portfolio' }}"><i class="la la-chart-bar nav-icon"></i> Review Portfolio</a></li>
@endif
<hr/>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('portfolio') }}'><i class='nav-icon la la-object-group'></i> Portfolios</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('initiative') }}'><i class='nav-icon la la-project-diagram'></i> Initiatives</a></li>
<hr/>

@if(Auth::user()->hasRole('Site Admin'))

    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('red-line') }}'><i class='nav-icon la la-exclamation-triangle'></i> Red lines</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('principle') }}"><i class="nav-icon la la-stream"></i> Principles</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('score-tag') }}"><i class="nav-icon la la-tag"></i> Score tags</a></li>
    <hr/>

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> Users</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role-invite') }}"><i class="nav-icon la la-envelope"></i> Admin User Invites</a></li>
    <hr/>

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('continent') }}"><i class="nav-icon la la-globe"></i> Continents</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('region') }}"><i class="nav-icon la la-arrows-alt"></i> Regions</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('country') }}"><i class="nav-icon la la-flag"></i> Countries</a></li>
@endif


