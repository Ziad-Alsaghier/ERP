@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="dash-sidebar light-sidebar">
    @else
        <nav class="dash-sidebar light-sidebar ">
@endif
<div class="navbar-wrapper">
    <div class="m-header main-logo">
        <a href="{{ route('home') }}" class="b-brand">
            @if ($setting['cust_darklayout'] && $setting['cust_darklayout'] == 'on')
                <img src="{{ asset($logo . '/' . (isset( $setting['company_logo_light']) && !empty( $setting['company_logo_light']) ?  $setting['company_logo_light'] : 'logo-dark.png')) }}"
                    alt="{{ env('APP_NAME') }}" class="logo logo-lg">
            @else
                <img src="{{ asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $setting['company_logo_dark'] : 'logo-light.png')) }}"
                    alt="{{ env('APP_NAME') }}" class="logo logo-lg">
            @endif

        </a>
    </div>
    <div class="navbar-content">
        {{-- Dashboards Menu --}}
        @if ($user->type != 'client' && $user->plan !== 0)
            <ul class="dash-navbar dark-mode">
                @if (
                        Gate::check('show hrm dashboard') ||
                        Gate::check('show project dashboard') ||
                        Gate::check('show account dashboard') ||
                        Gate::check('show crm dashboard') ||
                        Gate::check('show pos dashboard')
                    )
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon">
                                <i class="ti ti-home-2"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Dashboard') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @isset($userPlan->project)
                                
                            @if ($userPlan->project == 1)
                                {{-- Account Dashboard --}}
                                {{-- Important Permession --}}
                                {{-- UI --}}
                                @can('show account dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('design.show') }}">{{ __('UI') }}</a>
                                    </li>
                                @endcan
                                {{-- UI --}}
                                {{-- Important Permession --}}
                                @can('show account dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('dashboard') }}">{{ __('Accounts Dashboard') }}</a>
                                    </li>
                                @endcan
                                {{-- project Dashboard --}}
                                @can('show project dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('project.dashboard') }}">{{ __('Project Dashboard') }}</a>
                                    </li>
                                @endcan
                                {{-- CRM Dashboard --}}
                                @can('show crm dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('crm.dashboard') }}">{{ __('CRM Dashboard') }}</a>
                                    </li>
                                @endcan
                                {{-- hrm Dashboard --}}
                                @can('show hrm dashboard')
                                    <li
                                        class="dash-item">
                                        <a class="dash-link" href="{{ route('hrm.dashboard') }}">{{ __('HRM Dashboard') }}</a>
                                    </li>
                                @endcan
                                {{-- POS Dashboard --}}
                                @can('show pos dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('pos.dashboard') }}">{{ __('POS Dashboard') }}</a>
                                    </li>
                                    {{-- POS Dashboard --}}
                                @endcan
                            @endif
                            @endisset
                        </ul>
                    </li>
                @endif
                {{-- Manufacturing Config Menu --}}
                @if (Gate::check('show Manufacturing') &&
                        App\Models\User::where('id', '=', Auth::user()->creatorId())->first('plan')->plan == 9)
                    <li class="dash-item dash-hasmenu">
                        <a class="dash-link" href="#!">
                            <span class="dash-micon">
                                <i class="ti ti-settings"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Manufacturing') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            <li class="dash-item">
                                <a class="dash-link" target="_blank"
                                    href="{{ route('Manufacturing') }}">{{ __('Manufacturing Config') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link" target="_blank"
                                    href="{{ route('production.line.index') }}">{{ __('Production Lines') }}
                                </a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link" target="_blank"
                                    href="{{ route('production.line.type.index') }}">{{ __('Production Lines Types') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- Manage website Menu --}}
                @if (Gate::check('show order') && App\Models\User::where('id', '=', Auth::user()->creatorId())->first('plan')->plan == 9)
                    <li class="dash-item dash-hasmenu">
                        <a class="dash-link" href="#!">
                            <span class="dash-micon">
                                <i class="ti ti-world"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Manage website') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            {{-- <li class="dash-item">
                                <a class="dash-link" href="{{ route('web_users.index') }}">{{ __('Users') }}</a>
                            </li> --}}
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('web_orders.index') }}">{{ __('Orders') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- Sales Menu --}}
                @if (Gate::check('manage customer') ||
                        Gate::check('manage proposal') ||
                        Gate::check('manage invoice') ||
                        Gate::check('manage revenue') ||
                        Gate::check('manage credit note'))
                    <li
                        class="dash-item dash-hasmenu">
                        <a class="dash-link" href="#!">
                            <span class="dash-micon">
                                <i class="ti ti-shopping-cart"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Sales') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @if (Gate::check('manage customer'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('customer.index') }}">{{ __('Customers Management') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage proposal'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('proposal.index') }}">{{ __('Proposals') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage invoice'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage revenue'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('revenue.index') }}">{{ __('Revenue') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage credit note'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('credit.note') }}">{{ __('Credit Note') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{-- Purchases Menu --}}
                @if (Gate::check('manage vender') ||
                        Gate::check('manage bill') ||
                        Gate::check('manage payment') ||
                        Gate::check('manage debit note'))
                    <li class="dash-item dash-hasmenu">
                        <a class="dash-link" href="#!">
                            <span class="dash-micon">
                                <i class="ti ti-briefcase"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Purchases') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>

                        <ul class="dash-submenu">
                            @if (Gate::check('manage vender'))
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('vender.index') }}">{{ __('Suppilers Management') }}</a>
                                </li>
                            @endif
                            @can('manage quotation')
                                <li
                                    class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('quotation.index') }}">{{ __('Purchases Quotation') }}</a>
                                </li>
                            @endcan
                            @can('manage purchase')
                                <li
                                    class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('purchase.index') }}">{{ __('Purchases Orders') }}</a>
                                </li>
                            @endcan
                            @can('manage bill')
                                <li
                                    class="dash-item">
                                    <a class="dash-link" href="{{ route('bill.index') }}">{{ __('Bills') }}</a>
                                </li>
                            @endcan
                            @can('manage payment')
                                <li
                                    class="dash-item">
                                    <a class="dash-link" href="{{ route('payment.index') }}">{{ __('Payment') }}</a>
                                </li>
                            @endcan
                            @can('manage debit note')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('debit.note') }}">{{ __('Debit Note') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                {{-- Product Menu --}}
                @if (Gate::check('manage product & service'))
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-package"></i></span><span class="dash-mtext">{{ __('Products System') }}</span><span class="dash-arrow">
                                <i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @if (Gate::check('manage product & service'))
                                <li class="dash-item">
                                    <a href="{{ route('productservice.index') }}" class="dash-link">{{ __('Product & Services') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage product & service'))
                                <li class="dash-item">
                                    <a href="{{ route('productservice.attributes') }}" class="dash-link">{{ __('Product & Attributes') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{-- POS Menu --}}
                @if (Gate::check('manage pos'))
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-target"></i></span><span
                                class="dash-mtext">{{ __('POS System') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-down"></i></span></a>
                        <ul class="dash-submenu">
                            @can('show pos')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('pos.index') }}">{{ __('POS System') }}</a>
                                </li>
                            @endcan
                            @can('manage pos')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('pos.report') }}">{{ __('POS Detail') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                {{-- Warehouse --}}
                @if (!empty($userPlan) && $userPlan->pos == 1)
                    @if (Gate::check('manage warehouse'))
                        <li class="dash-item dash-hasmenu">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-smart-home"></i></span><span class="dash-mtext">{{ __('Warehouse') }}</span><span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage warehouse')
                                    <li class="dash-item">
                                        <a class="dash-link"
                                            href="{{ route('warehouse.index') }}">{{ __('Warehouse') }}</a>
                                    </li>
                                @endcan
                                @can('manage warehouse')
                                    <li class="dash-item">
                                        <a class="dash-link"
                                            href="{{ route('warehouse-transfer.index') }}">{{ __('Transfer Warehouse') }}</a>
                                    </li>
                                @endcan
                                @can('create barcode')
                                    <li class="dash-item">
                                        <a class="dash-link"
                                            href="{{ route('pos.barcode') }}">{{ __('Print Barcode') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif
                @endif
                {{-- CRM Menu --}}
                @if (!empty($userPlan) && $userPlan->crm == 1)
                    @if (Gate::check('manage lead') ||
                            Gate::check('manage deal') ||
                            Gate::check('manage form builder') ||
                            Gate::check('manage contract'))
                        <li class="dash-item dash-hasmenu">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-user-exclamation"></i></span><span class="dash-mtext">{{ __('CRM System') }}</span><span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage lead')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('leads.index') }}">{{ __('Leads') }}</a>
                                    </li>
                                @endcan
                                @can('manage deal')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('deals.index') }}">{{ __('Deals') }}</a>
                                    </li>
                                @endcan
                                @can('manage form builder')
                                    <li class="dash-item">
                                        <a class="dash-link"
                                            href="{{ route('form_builder.index') }}">{{ __('Form Builder') }}</a>
                                    </li>
                                @endcan
                                @can('manage contract')
                                    <li class="dash-item">
                                        <a class="dash-link"
                                            href="{{ route('contract.index') }}">{{ __('Contract') }}</a>
                                    </li>
                        @endif
                        @if (Gate::check('manage lead stage') ||
                                Gate::check('manage pipeline') ||
                                Gate::check('manage source') ||
                                Gate::check('manage label') ||
                                Gate::check('manage stage'))
                            <li class="dash-item">
                                <a class="dash-link"  href="{{ route('pipelines.index') }}   ">{{ __('CRM System Setup') }}</a>
                            </li>
                        @endif
                </ul>
                </li>
            @endif
            @endif
            {{-- Project menu --}}
            @if (!empty($userPlan) && $userPlan->project == 1)
                @if (Gate::check('manage project'))
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-share"></i></span><span class="dash-mtext">{{ __('Project System') }}</span><span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage project')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
                                </li>
                            @endcan
                            @can('manage project task')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('taskBoard.view', 'list') }}">{{ __('Tasks') }}</a>
                                </li>
                            @endcan
                            @can('manage timesheet')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('timesheet.list') }}">{{ __('Timesheet') }}</a>
                                </li>
                            @endcan
                            @can('manage bug report')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('bugs.view', 'list') }}">{{ __('Bug') }}</a>
                                </li>
                            @endcan
                            @can('manage project task')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('task.calendar', ['all']) }}">{{ __('Task Calendar') }}</a>
                                </li>
                            @endcan
                            @if (\Auth::user()->type != 'super admin')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('time.tracker') }}">{{ __('Tracker') }}</a>
                                </li>
                            @endif
                            @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'Employee')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('project_report.index') }}">{{ __('Project Report') }}</a>
                                </li>
                            @endif

                            @if (Gate::check('manage project task stage') || Gate::check('manage bug status'))
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Project System Setup') }}<span
                                            class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage project task stage')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('project-task-stages.index') }}">{{ __('Project Task Stages') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage bug status')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('bugstatus.index') }}">{{ __('Bug Status') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif
            {{-- Accounting System Menu --}}
            @if (!empty($userPlan) && $userPlan->account == 1)
                @if (Gate::check('manage bank account') ||
                        Gate::check('manage bank transfer') ||
                        Gate::check('manage chart of account') ||
                        Gate::check('manage journal entry') ||
                        Gate::check('manage chart of account') ||
                        Gate::check('manage journal entry'))
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon">
                                <i class="ti ti-calculator"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Accounting System ') }} </span>
                            <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('account.index') }}">{{ __('Chart of Accounts') }}</a>
                            </li>
                            {{-- <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'fixed-assets' ? 'active dash-trigger' : '' }}">
                                <a class="dash-link" href="#">{{ __('Fixed Assets') }}<span class="dash-arrow"><i
                                            data-feather="chevron-down"></i></span></a>
                                <ul class="dash-submenu">
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'fixed-assets.index' || Request::route()->getName() == 'fixed-assets.create' || Request::route()->getName() == 'fixed-assets.edit' ? ' active' : '' }}">
                                        <a class="dash-link" href="{{ route('fixed-assets.index') }}">{{ __('Fixed Assets') }}</a>
                                    </li>
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'asset_categories.index' || Request::route()->getName() == 'asset_categories.create' || Request::route()->getName() == 'asset_categories.edit' ? ' active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('asset_categories.index') }}">{{ __('Asset Categories') }}</a>
                                    </li>
                                </ul>
                            </li> --}}
                            @if (Gate::check('manage bank account') || Gate::check('manage bank transfer'))
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Bank Accounts') }}<span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('bank-account.index') }}">{{ __('Accounts') }}</a>
                                        </li>
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('bank-transfer.index') }}">{{ __('Transfer') }}</a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('journal-entry.index') }}">{{ __('Journal Account') }}</a>
                            </li>
                            @can('manage expense')
                                <li
                                    class="dash-item">
                                    <a class="dash-link" href="{{ route('expense.index') }}">{{ __('Expense') }}</a>
                                </li>
                            @endcan
                            @if (\Auth::user()->type == 'company')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('budget.index') }}">{{ __('Budget Planner') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage goal'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('goal.index') }}">{{ __('Financial Goal') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage constant tax') ||
                                    Gate::check('manage constant category') ||
                                    Gate::check('manage constant unit') ||
                                    Gate::check('manage constant payment method') ||
                                    Gate::check('manage constant custom field'))
                                <li class="dash-item">
                                    <a class="dash-link"  href="{{ route('taxes.index') }}">{{ __('Accounting Setup') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('manage print settings'))
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('print.setting') }}">{{ __('Print Settings') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif
            {{-- HRM Menu --}}
            @if (!empty($userPlan) && $userPlan->hrm == 1)
                @if (Gate::check('manage employee') || Gate::check('manage setsalary'))
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon">
                                <i class="ti ti-users"></i>
                            </span>
                            <span class="dash-mtext">
                                {{ __('HRM System') }}
                            </span>
                            <span class="dash-arrow">
                                <i data-feather="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dash-submenu">
                            <li class="dash-item ">
                                @if (\Auth::user()->type == 'Employee')
                                    @php
                                        $employee = App\Models\Employee::where('user_id', \Auth::user()->id)->first();
                                    @endphp
                                    <a class="dash-link" href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ __('Employee') }}</a>
                                @else
                                    <a href="{{ route('employee.index') }}" class="dash-link">{{ __('Employee Setup') }}</a>
                                @endif
                            </li>
                            @if (Gate::check('manage set salary') || Gate::check('manage pay slip'))
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Payroll Setup') }}<span
                                            class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage set salary')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('setsalary.index') }}">{{ __('Set salary') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage pay slip')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('payslip.index') }}">{{ __('Payslip') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @can('manage attendance')
                                <li class="dash-item dash-hasmenu" href="#navbar-attendance" data-toggle="collapse" role="button">
                                    <a class="dash-link" href="#">{{ __('Attendance') }}<span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('attendanceemployee.index') }}">{{ __('Mark Attendance') }}</a>
                                        </li>
                                        @can('create attendance')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('attendanceemployee.bulkattendance') }}">{{ __('Bulk Attendance') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @if (Gate::check('manage leave') || Gate::check('manage attendance'))
                                <li
                                    class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Leave Management Setup') }}<span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage leave')
                                            <li  class="dash-item">
                                                <a class="dash-link" href="{{ route('leave.index') }}">{{ __('Manage Leave') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @if (Gate::check('manage indicator') || Gate::check('manage appraisal') || Gate::check('manage goal tracking'))
                                <li class="dash-item dash-hasmenu" href="#navbar-performance" data-toggle="collapse" role="button" >
                                    <a class="dash-link" href="#">{{ __('Performance Setup') }}<span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage indicator')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('indicator.index') }}">{{ __('Indicator') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage appraisal')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('appraisal.index') }}">{{ __('Appraisal') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage goal tracking')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('goaltracking.index') }}">{{ __('Goal Tracking') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @if (Gate::check('manage training') || Gate::check('manage trainer') || Gate::check('show training'))
                                <li class="dash-item dash-hasmenu" href="#navbar-training" data-toggle="collapse" role="button">
                                    <a class="dash-link" href="#">{{ __('Training Setup') }}<span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage training')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('training.index') }}">{{ __('Training List') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage trainer')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('trainer.index') }}">{{ __('Trainer') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @if (Gate::check('manage job') ||
                                    Gate::check('create job') ||
                                    Gate::check('manage job application') ||
                                    Gate::check('manage custom question') ||
                                    Gate::check('show interview schedule') ||
                                    Gate::check('show career'))
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Recruitment Setup') }}<span
                                            class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                                    <ul class="dash-submenu">
                                        @can('manage job')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('job.index') }}">{{ __('Jobs') }}</a>
                                            </li>
                                        @endcan
                                        @can('create job')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('job.create') }}">{{ __('Job Create') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage job application')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('job-application.index') }}">{{ __('Job Application') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage job application')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('job.application.candidate') }}">{{ __('Job Candidate') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage job application')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('job.on.board') }}">{{ __('Job On-boarding') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage custom question')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('custom-question.index') }}">{{ __('Custom Question') }}</a>
                                            </li>
                                        @endcan
                                        @can('show interview schedule')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('interview-schedule.index') }}">{{ __('Interview Schedule') }}</a>
                                            </li>
                                        @endcan
                                        @can('show career')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('career', [\Auth::user()->creatorId(), $lang]) }}">{{ __('Career') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @if (Gate::check('manage award') ||
                                    Gate::check('manage transfer') ||
                                    Gate::check('manage resignation') ||
                                    Gate::check('manage travel') ||
                                    Gate::check('manage promotion') ||
                                    Gate::check('manage complaint') ||
                                    Gate::check('manage warning') ||
                                    Gate::check('manage termination') ||
                                    Gate::check('manage announcement') ||
                                    Gate::check('manage holiday'))
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('HR Admin Setup') }}
                                        <span class="dash-arrow"><i data-feather="chevron-down"></i></span>
                                    </a>
                                    <ul class="dash-submenu">
                                        @can('manage award')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('award.index') }}">{{ __('Award') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage transfer')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('transfer.index') }}">{{ __('Transfer') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage resignation')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('resignation.index') }}">{{ __('Resignation') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage travel')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('travel.index') }}">{{ __('Trip') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage promotion')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('promotion.index') }}">{{ __('Promotion') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage complaint')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('complaint.index') }}">{{ __('Complaints') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage warning')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('warning.index') }}">{{ __('Warning') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage termination')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('termination.index') }}">{{ __('Termination') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage announcement')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('announcement.index') }}">{{ __('Announcement') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage holiday')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('holiday.index') }}">{{ __('Holidays') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                            @can('manage event')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('event.index') }}">{{ __('Event Setup') }}</a>
                                </li>
                            @endcan
                            @can('manage meeting')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('meeting.index') }}">{{ __('Meeting') }}</a>
                                </li>
                            @endcan
                            @can('manage assets')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('account-assets.index') }}">{{ __('Employees Asset Setup ') }}</a>
                                </li>
                            @endcan
                            @can('manage document')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('document-upload.index') }}">{{ __('Document Setup') }}</a>
                                </li>
                            @endcan
                            @can('manage company policy')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('company-policy.index') }}">{{ __('Company policy') }}</a>
                                </li>
                            @endcan

                            @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'HR')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('branch.index') }}">{{ __('HRM System Setup') }}</a>
                                </li>
                            @endcan


                    </ul>
                </li>
            @endif
        @endif
        {{-- User Management --}}
        @if (
            \Auth::user()->type != 'super admin' &&
                (Gate::check('manage user') || Gate::check('manage role') || Gate::check('manage client')))
            <li class="dash-item dash-hasmenu">
                <a href="#!" class="dash-link "><span class="dash-micon"><i class="ti ti-user-check"></i></span><span class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i data-feather="chevron-down"></i></span></a>
                <ul class="dash-submenu">
                    @can('manage user')
                        <li class="dash-item">
                            <a class="dash-link" href="{{ route('users.index') }}">{{ __('User') }}</a>
                        </li>
                    @endcan
                    @can('manage role')
                        <li class="dash-item">
                            <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Role') }}</a>
                        </li>
                    @endcan
                    @can('manage client')
                        <li class="dash-item">
                            <a class="dash-link" href="{{ route('clients.index') }}">{{ __('Client') }}</a>
                        </li>
                    @endcan
                    {{-- @can('manage user')
                    <li class="dash-item {{ (Request::route()->getName() == 'users.index' || Request::segment(1) == 'users' || Request::route()->getName() == 'users.edit') ? ' active' : '' }}">
                        <a class="dash-link" href="{{ route('user.userlog') }}">{{__('User Logs')}}</a>
                    </li>
                    @endcan --}}
                </ul>
            </li>
        @endif
        {{-- Report & Support  --}}
        @if (\Auth::user()->type != 'super admin')
            {{-- Start Make All Report Financial --}}
            <li
                class="dash-item dash-hasmenu">
                <a href="{{ route('report.financial_reports') }}" class="dash-link">
                    <span class="dash-micon"><i class="ti ti-report"></i></span><span class="dash-mtext">{{ __('Reports') }}</span>
                </a>
            </li>
            {{-- End Make All Report Financial --}}
            <li class="dash-item dash-hasmenu">
                <a href="{{ route('support.index') }}" class="dash-link">
                    <span class="dash-micon"><i class="ti ti-headphones"></i></span><span class="dash-mtext">{{ __('Support System') }}</span>
                </a>
            </li>
            <li
                class="dash-item dash-hasmenu">
                <a href="{{ route('zoom-meeting.index') }}" class="dash-link">
                    <span class="dash-micon"><i class="ti ti-brand-skype"></i></span><span class="dash-mtext">{{ __('Zoom Meeting') }}</span>
                </a>
            </li>
            {{-- <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'chats' ? 'active' : '' }}">
                        <a href="{{ url('chats') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-message-circle"></i></span><span
                                class="dash-mtext">{{ __('Messenger') }}</span>
                        </a>
                    </li> --}}
        @endif
        @if (\Auth::user()->type == 'company')
            <li class="dash-item dash-hasmenu ">
                <a href="{{ route('notification-templates.index') }}" class="dash-link">
                    <span class="dash-micon"><i class="ti ti-notification"></i></span><span class="dash-mtext">{{ __('Notification Template') }}</span>
                </a>
            </li>
        @endif
        {{-- Settings --}}
        @if (\Auth::user()->type != 'super admin')
            @if (Gate::check('manage company plan') || Gate::check('manage order') || Gate::check('manage company settings'))
                <li class="dash-item dash-hasmenu">
                    <a href="#!" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">{{ __('Settings') }}</span>
                        <span class="dash-arrow">
                            <i data-feather="chevron-down"></i></span>
                    </a>
                    <ul class="dash-submenu">
                        @if (Gate::check('manage company settings'))
                            <li class="dash-item dash-hasmenu">
                                <a href="{{ route('settings') }}"
                                    class="dash-link">{{ __('System Settings') }}</a>
                            </li>
                        @endif
                        @if (Gate::check('manage company plan'))
                            <li class="dash-item">
                                <a href="{{ route('plans.index') }}"
                                    class="dash-link">{{ __('Setup Subscription Plan') }}</a>
                            </li>
                        @endif

                        @if (Gate::check('manage order') && Auth::user()->type == 'company')
                            <li class="dash-item">
                                <a href="{{ route('order.index') }}" class="dash-link">{{ __('Order') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        @endif
        </ul>
        @endif
        @if (\Auth::user()->type == 'client')
            <ul class="dash-navbar dark-mode">
                @if (Gate::check('manage client dashboard'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('client.dashboard.view') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage deal'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('deals.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-rocket"></i></span><span class="dash-mtext">{{ __('Deals') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage contract'))
                    <li
                        class="dash-item dash-hasmenu">
                        <a href="{{ route('contract.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-rocket"></i></span><span class="dash-mtext">{{ __('Contract') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('projects.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-share"></i></span><span class="dash-mtext">{{ __('Project') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project'))
                    <li
                        class="dash-item">
                        <a class="dash-link" href="{{ route('project_report.index') }}">
                            <span class="dash-micon"><i class="ti ti-chart-line"></i></span><span  class="dash-mtext">{{ __('Project Report') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project task'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('taskBoard.view', 'list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-list-check"></i></span><span class="dash-mtext">{{ __('Tasks') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage bug report'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('bugs.view', 'list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-bug"></i></span><span class="dash-mtext">{{ __('Bugs') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage timesheet'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('timesheet.list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-clock"></i></span><span class="dash-mtext">{{ __('Timesheet') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project task'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('task.calendar', ['all']) }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-calendar"></i></span><span class="dash-mtext">{{ __('Task Calender') }}</span>
                        </a>
                    </li>
                @endif

                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('support.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-headphones"></i></span><span
                            class="dash-mtext">{{ __('Support') }}</span>
                    </a>
                </li>
            </ul>
        @endif
        @if (\Auth::user()->type == 'super admin')
            <ul class="dash-navbar dark-mode">
                @if (Gate::check('manage super admin dashboard'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('client.dashboard.view') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif
                @can('manage user')
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('users.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-users"></i></span><span
                                class="dash-mtext">{{ __('Companies') }}</span>
                        </a>
                    </li>
                @endcan
                @if (Gate::check('manage plan'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('plans.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-trophy"></i></span><span
                                class="dash-mtext">{{ __('Plan') }}</span>
                        </a>
                    </li>
                @endif
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('plan_request.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-arrow-up-right-circle"></i></span><span
                                class="dash-mtext">{{ __('Plan Request') }}</span>
                        </a>
                    </li>
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('meet.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-arrow-up-right-circle"></i></span><span
                                class="dash-mtext">{{ __('Meet Track') }}</span>
                        </a>
                    </li>
                @if (Gate::check('manage coupon'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('coupons.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                class="dash-mtext">{{ __('Coupon') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage order'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('order.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-shopping-cart-plus"></i></span><span
                                class="dash-mtext">{{ __('Order') }}</span>
                        </a>
                    </li>
                @endif
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('manage.email.language', [$emailTemplate->id, \Auth::user()->lang]) }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-template"></i></span>
                        <span class="dash-mtext">{{ __('Email Template') }}</span>
                    </a>
                </li>

                @if (\Auth::user()->type == 'super admin')
                    @include('landingpage::menu.landingpage')
                @endif

                @if (Gate::check('manage permission'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('permissions.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-cloud"></i></span><span
                                class="dash-mtext">{{ __('Manage Permissions') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage system settings'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('systems.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        @endif


        {{-- momen edit [2024-8-14] --}}
        @if (\Auth::user()->type != 'client' && \Auth::user()->type != 'super admin' && Auth::user()->plan == 0)
            @if (Gate::check('manage order') || Gate::check('manage order'))
                <ul class="dash-navbar dark-mode">
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span>
                            <span class="dash-arrow">
                                <i data-feather="chevron-down"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @if (Gate::check('manage company plan'))
                                <li
                                    class="dash-item{{ Request::route()->getName() == 'plans.index' || Request::route()->getName() == 'stripe' ? ' active' : '' }}">
                                    <a href="{{ route('plans.index') }}"
                                        class="dash-link">{{ __('Setup Subscription Plan') }}</a>
                                </li>
                            @endif

                            @if (Gate::check('manage order') && Auth::user()->type == 'company')
                                <li class="dash-item {{ Request::segment(1) == 'order' ? 'active' : '' }}">
                                    <a href="{{ route('order.index') }}" class="dash-link">{{ __('Order') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
        @endif

        @if (\Auth::user()->type == 'company')
            @if (isset(\Auth::user()->trial_expire_date) && \Auth::user()->trial_expire_date > Carbon\Carbon::now())
                <div class="navbar-footer border-top ">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom">
                        <div class="me-2">
                            <svg width="30px" height="30px" viewBox="-2.4 -2.4 28.80 28.80" fill="none"
                                xmlns="http://www.w3.org/2000/svg" stroke="#ffd500">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                    <rect x="-2.4" y="-2.4" width="28.80" height="28.80" rx="14.4"
                                        fill="#ffa21d" strokewidth="0"></rect>
                                </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path
                                        d="M4.51555 7C3.55827 8.4301 3 10.1499 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3V6M12 12L8 8"
                                        stroke="#000000" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </div>
                        <div>
                            <b class="d-block f-w-700">{{ __('Trial Plan Expiration Date') }}</b>
                            <span>{{ \Auth::user()->trial_expire_date }} </span>
                        </div>
                    </div>
                </div>
            @endif


            @if (isset(\Auth::user()->plan_expire_date) &&
                    \Auth::user()->plan_expire_date > 0 &&
                    !isset(\Auth::user()->trial_expire_date))
                <div class="navbar-footer border-top ">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom">
                        <div class="me-2">
                            <svg width="30px" height="30px" viewBox="-3.12 -3.12 30.24 30.24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                    <rect x="-3.12" y="-3.12" width="30.24" height="30.24" rx="15.12"
                                        fill="#7eec9f" strokewidth="0"></rect>
                                </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path
                                        d="M4.51555 7C3.55827 8.4301 3 10.1499 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3V6M12 12L8 8"
                                        stroke="#000000" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </div>
                        <div>


                            @if (\Auth::user()->plan_expire_date < Carbon\Carbon::now())
                                <span
                                    class="text-warning">{{ __('Please pay the subscription before the grace period ends') }}
                                </span> <br>
                                <a href="{{ route('plans.index') }}"
                                    class="btn btn-warning mt-3 w-100">{{ __('Pay
                                                                                                                                                                                                                                                                                                                                                                        Now') }}</a>
                            @else
                                <b class="d-block f-w-700">{{ __('Subscription renewal date') }}</b>
                                <span>{{ \Auth::user()->plan_expire_date }} </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        @endif



        <div class="navbar-footer border-top ">
            <a href="#" class="d-flex align-items-center py-3 px-3 border-bottom">
                <div class="me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30" viewBox="0 0 29 30"
                        fill="none">
                        <circle cx="14.5" cy="15.1846" r="14.5" fill="#6FD943"></circle>
                        <path opacity="0.4"
                            d="M22.08 8.66459C21.75 8.28459 21.4 7.92459 21.02 7.60459C19.28 6.09459 17 5.18461 14.5 5.18461C12.01 5.18461 9.73999 6.09459 7.98999 7.60459C7.60999 7.92459 7.24999 8.28459 6.92999 8.66459C5.40999 10.4146 4.5 12.6946 4.5 15.1846C4.5 17.6746 5.40999 19.9546 6.92999 21.7046C7.24999 22.0846 7.60999 22.4446 7.98999 22.7646C9.73999 24.2746 12.01 25.1846 14.5 25.1846C17 25.1846 19.28 24.2746 21.02 22.7646C21.4 22.4446 21.75 22.0846 22.08 21.7046C23.59 19.9546 24.5 17.6746 24.5 15.1846C24.5 12.6946 23.59 10.4146 22.08 8.66459ZM14.5 19.6246C13.54 19.6246 12.65 19.3146 11.93 18.7946C11.52 18.5146 11.17 18.1646 10.88 17.7546C10.37 17.0346 10.06 16.1346 10.06 15.1846C10.06 14.2346 10.37 13.3346 10.88 12.6146C11.17 12.2046 11.52 11.8546 11.93 11.5746C12.65 11.0546 13.54 10.7446 14.5 10.7446C15.46 10.7446 16.35 11.0546 17.08 11.5646C17.49 11.8546 17.84 12.2046 18.13 12.6146C18.64 13.3346 18.95 14.2346 18.95 15.1846C18.95 16.1346 18.64 17.0346 18.13 17.7546C17.84 18.1646 17.49 18.5146 17.08 18.8046C16.35 19.3146 15.46 19.6246 14.5 19.6246Z"
                            fill="#162C4E"></path>
                        <path
                            d="M22.08 8.66459L18.18 12.5746C18.16 12.5846 18.15 12.6046 18.13 12.6146C17.84 12.2046 17.49 11.8546 17.08 11.5646C17.09 11.5446 17.1 11.5346 17.12 11.5146L21.02 7.60459C21.4 7.92459 21.75 8.28459 22.08 8.66459Z"
                            fill="#162C4E"></path>
                        <path
                            d="M11.9297 18.7947C11.9197 18.8147 11.9097 18.8347 11.8897 18.8547L7.98969 22.7647C7.60969 22.4447 7.24969 22.0847 6.92969 21.7047L10.8297 17.7947C10.8397 17.7747 10.8597 17.7647 10.8797 17.7547C11.1697 18.1647 11.5197 18.5147 11.9297 18.7947Z"
                            fill="#162C4E"></path>
                        <path
                            d="M11.9297 11.5746C11.5197 11.8546 11.1697 12.2045 10.8797 12.6145C10.8597 12.6045 10.8497 12.5846 10.8297 12.5746L6.92969 8.66453C7.24969 8.28453 7.60969 7.92453 7.98969 7.60453L11.8897 11.5146C11.9097 11.5346 11.9197 11.5546 11.9297 11.5746Z"
                            fill="#162C4E"></path>
                        <path
                            d="M22.08 21.7046C21.75 22.0846 21.4 22.4446 21.02 22.7646L17.12 18.8546C17.1 18.8346 17.09 18.8246 17.08 18.8046C17.49 18.5146 17.84 18.1646 18.13 17.7546C18.15 17.7646 18.16 17.7746 18.18 17.7946L22.08 21.7046Z"
                            fill="#162C4E"></path>
                    </svg>
                </div>
                <div>
                    <b class="d-block f-w-700">{{ __('You need help?') }}</b>
                    <span>{{ __('Contact Us') }} </span>
                </div>
            </a>
        </div>


    </div>
</div>

</nav>
