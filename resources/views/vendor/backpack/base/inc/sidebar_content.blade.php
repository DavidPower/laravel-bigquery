<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('manual-payment') }}'><i class="nav-icon las la-file-invoice-dollar"></i>Manual payments</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('batch') }}'><i class='nav-icon la la-history'></i> Batches</a></li>
<p class="mt10">
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('logout') }}'><i class="nav-icon las la-sign-out-alt"></i>Logout</a></li>
