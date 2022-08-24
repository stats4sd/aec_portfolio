<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('project') }}'><i class='nav-icon la la-question'></i> Projects</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('red-line') }}'><i class='nav-icon la la-question'></i> Red lines</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('principle') }}"><i class="nav-icon la la-question"></i> Principles</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('score-tag') }}"><i class="nav-icon la la-question"></i> Score tags</a></li>