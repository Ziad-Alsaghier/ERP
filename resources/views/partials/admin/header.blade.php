@php
$users=\Auth::user();
$profile=\App\Models\Utility::get_file('uploads/avatar/');
$languages=\App\Models\Utility::languages();

$lang = isset($users->lang)?$users->lang:'en';
if ($lang == null) {
$lang = 'en';
}
if (!function_exists('formatNotification')) {
function formatNotification($type, $action) {
// Handle special cases for actions
switch ($action) {
case 'delete':
$formattedAction = 'deleted';
break;
case 'create':
$formattedAction = 'created';
break;
case 'edit':
$formattedAction = 'edited';
break;
default:
$formattedAction = $action . 'ed';
break;
}

return ucfirst(__($type)) . ' '. __('has been') .' '  . __($formattedAction);
}
}

// $LangName = \App\Models\Language::where('code',$lang)->first();
// $LangName =\App\Models\Language::languageData($lang);
$LangName = cache()->remember('full_language_data_' . $lang, now()->addHours(24), function () use ($lang) {
return \App\Models\Language::languageData($lang);
});

$setting = \App\Models\Utility::settings();

$unseenCounter=App\Models\ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count();

//Momen Here -->
$notifications = \App\Models\Notification::where('creator_id',\Auth::user()->creatorId())->get();
$unreadCount = $notifications->where('is_read', 0)->count();

@endphp
<header class="dash-header {{ isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on' ? 'header-bg' : '' }} " style="box-shadow:inset 0px 0px 110px 5px black;">
                            {{--  Latest Edit at 2/25/2025 --}}
<style>
        /* Notification Panel Styling */
        .offcanvas {
            background: rgba(255, 255, 255, 0.1); /* White with blur effect */
            backdrop-filter: blur(3px); /* Apply blur effect */
            color: white;
            width: 400px;
        }

        .offcanvas-header {
            border-bottom: 1px solid #444;
        }
        /* Notification Card */
        .notification-card {
            position: relative;
            border-radius: 16px;
            padding: 20px;
            background: linear-gradient(145deg, #2a2e37, #1d5c9b);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .notification-card:hover {
            transform: translateY(-5px);
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 1px;
            right: 5px;
            font-size: 24px;
            color: #ff4d4d;
            text-decoration: none;
            margin-top: 2px
        }
        /* Notification Content */
        .notification-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }
        /* Badge Colors */
        .action-badge.create {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }

        .action-badge.delete {
            background: linear-gradient(135deg, #E53935, #D32F2F);
        }

        .action-badge.update {
            background: linear-gradient(135deg, #1E88E5, #1976D2);
        }
        .offcanvas-title{
            color: white;
        }
        /* Message and Description */
        .message {
            font-size: 16px;
            font-weight: bold;
        }

        .description {
            color: #B2C9FF;
            font-size: 14px;
        }

        /* Time Details */
        .time-details small {
            display: block;
            color: #888;
            font-size: 12px;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .offcanvas {
                width: 100%;
            }

            .animated-dropdown {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            visibility: hidden;
            }

            .dropdown.show .animated-dropdown {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            }

            .dropdown-item {
            transition: all 0.2s ease-in-out;
            border-radius: 4px;
            }

            .dropdown-item:hover {
            background-color: #f3f4f6;
            transform: translateX(3px);
            }

            .flag-icon {
            width: 20px;
            height: 14px;
            background-size: cover;
            border-radius: 3px;
            box-shadow: 0 0 2px rgba(0,0,0,0.1);
            display: inline-block;
            }

            .drp-language .dropdown-item span {
            display: inline-block;
            }

            .dropdown-divider {
            border-top: 1px solid #ddd;
            }
        }
    </style>
<div class="header-wrapper" style="background-image: url('{{ asset('assets/images/bg-pattern.png') }}'); background-size: cover; height: 100%; padding-left: 25px;">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="theme-avtar">
                            <img src="{{ asset(!empty(\Auth::user()->avatar) ? url($profile) . '/' . \Auth::user()->avatar :  url($profile).'/'.'avatar.png')}}" class="img-fluid rounded-circle">
                        </span>
                        <span class="hide-mob ms-2">{{__('Hi, ')}}{{\Auth::user()->name }}!</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    @if (Auth::user()->type == 'super admin' )
                    <a class="btn btn-primary ms-3" href="{{route('home.landingpage')}}">
                        <i class="ti ti-brand-chrome"></i><span class="hide-mob ms-2">{{__('show website')}}</span>
                    </a>
                    @endif


                    <div class="dropdown-menu dash-h-dropdown">

                        <a href="{{route('profile')}}" class="dropdown-item">
                            <i class="ti ti-user text-dark"></i><span>{{__('Profile')}}</span>
                        </a>

                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="dropdown-item">
                            <i class="ti ti-power text-dark"></i><span>{{__('Logout')}}</span>
                        </a>

                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            {{ csrf_field() }}
                        </form>

                    </div>
                </li>

            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                @php
                $manager = app('impersonate');
                @endphp

                {{-- @if(\Auth::user()->type == 'company' && $manager->getImpersonatorId() == 1 ) --}}
                @impersonating($guard = null)
                <li class="dropdown dash-h-item drp-company">
                    <a class="btn btn-danger btn-sm me-3" href="{{ route('exit.company') }}"><i class="ti ti-link"></i>
                        {{ __('Main Account') }}
                    </a>
                </li>
                @endImpersonating
                {{-- @endif --}}

                @php
                $accounts = App\Models\User::where('email',Auth::user()->email)->get();


                @endphp



                @if ($accounts->count() > 1)
           <li class="dropdown dash-h-item">
                    <a class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-user-check fs-5"></i>
                        <span class="drp-text hide-mob text-capitalize fw-semibold">{{ \Auth::user()->type }}</span>
                        <i class="ti ti-chevron-down drp-arrow"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow-lg p-3" style="width: 320px; z-index: 1999;">
                        <div class="text-center text-muted fw-bold mb-2 small">
                            {{ __('🔄 Switch Account') }}
                        </div>
                        <div class="dropdown-divider mb-2"></div>

                        @foreach ($accounts as $account)
                        @if ($account->id !== \Auth::user()->id)
                        <a href="{{ route('login.with.company', $account->id) }}"
                            class="dropdown-item d-flex align-items-start gap-3 p-2 rounded hover-bg-light"
                            style="transition: all 0.2s ease-in-out;">
                            <img src="{{ !empty($account->avatar) ? url($profile . '/' . $account->avatar) : asset($profile . 'avatar.png') }}"
                                alt="Avatar" class="rounded-circle border border-2 shadow-sm" width="45" height="45"
                                style="object-fit: cover;">

                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark mb-1">{{ $account->name }}</div>
                                <div class="small text-muted">
                                    <span class="badge bg-primary text-white text-capitalize">{{ $account->type }}</span>
                                    @if ($account->created_by != 1)
                                    <span class="d-block text-muted mt-1">
                                        {{ __('Created by') }}: <strong>{{ App\Models\User::find($account->created_by)->name ?? '—'
                                            }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </li>
                @endif

                @if( \Auth::user()->type !='client' && \Auth::user()->type !='super admin' )
                <li class="dropdown dash-h-item drp-notification">
                    <a class="dash-head-link arrow-none me-0" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                        <i class="ti ti-bell"></i>
                        <span class="bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep"> {{ $unreadCount }}<span
                                class="sr-only"></span>
                        </span>
                    </a>
                </li>

                {{-- ----------------------------------------------------------------------- --}}

                            {{--  Latest Edit at 2/25/2025 --}}
                <div dir="{{ $lang == 'ar' ? 'rtl' : 'ltr' }}" class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('Notification Message') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body scrollBar" style="overflow-y: auto; max-height: 80vh;">
                        <div class="latest-notification-icon">
                            <i class="ti ti-bell"></i>
                            <span class="bg-danger dash-h-badge message-toggle-msg message-counter custom_messanger_counter beep"> {{ $unreadCount }}<span class="sr-only"></span></span>
                        </div>
                        <div class="latest-notification-time">
                            <small>{{ $notifications?->sortByDesc('created_at')->first()?->created_at?->diffForHumans() ?? Null }}</small>
                        </div>
                        <div id="notification-list">
                            @foreach ($notifications->sortByDesc('created_at') as $noti)
                                @php
                                    $decodedData = json_decode($noti->data);
                                    $formattedMessage = formatNotification($noti->type, $decodedData->action);
                                @endphp

                                @if($noti->is_read == 0)
                                    <div class="notification-card mt-3" id="notification-{{ $noti->id }}">
                                        <a href="javascript:void(0);" class="close-btn" onclick="markAsSeen({{ $noti->id }})">
                                            <i class="ti ti-square-x" style="color: white;"></i>
                                        </a>
                                        <div class="notification-content mt-3">
                                            <span class="badge action-badge {{ ucfirst($decodedData->action) == 'Create' ? 'create' : (ucfirst($decodedData->action) == 'Delete' ? 'delete' : 'update') }}">
                                                {{ ucfirst(__($decodedData->action)) }}
                                            </span>

                                            <p class="message">{{ __($formattedMessage) }}</p>

                                            <p class="description">{{ $decodedData->description ?? __('notification.seen') }}</p>

                                            <div class="time-details">
                                                <small style="color: #FFD700;">{{ $noti->created_at->diffForHumans() }}</small>
                                                <small style="color: #FF69B4;">{{ __('Time') }}: {{ $noti->created_at->format('h:i A') }}</small>
                                                <small style="{{ $noti->created_at->isToday() ? 'color: #FF4500;' : 'color: #FF4500;' }}">{{ __('Date') }}: {{ $noti->created_at->format('Y/m/d') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    {{-- Make Design For Scroll Bar Notification  --}}
                    <style>
                        .scrollBar::-webkit-scrollbar {
                            width: 8px;
                            height: 20px;
                        }
                        .scrollBar::-webkit-scrollbar-thumb {
                            background-color: #888;
                            border-radius: 10px;
                        }
                        .scrollBar::-webkit-scrollbar-thumb:hover {
                            background-color: #555;
                        }
                    </style>
                </div>





                {{-- ----------------------------------------------------------------------- --}}
                @endif

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0 d-flex align-items-center gap-2"
                       data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world fs-5 text-primary"></i>
                        <span class="drp-text hide-mob fw-semibold">{{ ucfirst($LangName->full_name) }}</span>
                        <i class="ti ti-chevron-down drp-arrow hide-mob"></i>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end animated-dropdown shadow rounded" style="z-index: 1999; min-width: 220px;">
                        @foreach ($languages as $code => $language)
                            <a href="{{ route('change.language', $code) }}"
                               class="dropdown-item d-flex align-items-center gap-2 {{ $lang == $code ? 'text-primary fw-bold' : '' }}">
                                @if ($code === 'ar')
                                <span class="flag-icon" style="background-image: https://www.countryflags.com/eg.png';"></span>
                                @elseif ($code === 'en')
                                    <span class="flag-icon" style="background-image: https://www.countryflags.com/gb.png';"></span>
                                @else
                                    <span class="flag-icon bg-secondary"></span>
                                @endif
                                <span>{{ ucfirst($language) }}</span>
                            </a>
                        @endforeach

                        @if(\Auth::user()->type == 'super admin')
                            <div class="dropdown-divider my-2"></div>
                            <div class="px-3 text-muted small">{{ __('Super Admin Tools') }}</div>
                            <a data-url="{{ route('create.language') }}"
                               class="dropdown-item text-primary"
                               data-ajax-popup="true"
                               data-title="{{ __('Create New Language') }}">
                                <i class="ti ti-plus me-1"></i>{{ __('Create Language') }}
                            </a>
                            <a class="dropdown-item text-primary"
                               href="{{ route('manage.language', [$lang ?? 'english']) }}">
                                <i class="ti ti-settings me-1"></i>{{ __('Manage Language') }}
                            </a>
                        @endif
                    </div>
                </li>

            </ul>
        </div>
    </div>

    <script>
        // Show a notification toast
        function markAsSeen(notificationId) {
            $.ajax({
                url: `api/notification/seen/${notificationId}`, // Ensure correct URL format
                type: "GET", // Use GET method
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pass CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        // Hide the notification smoothly
                        $('#notification-' + notificationId).fadeOut('slow', function() {
                            $(this).remove();
                        });
                        // Show success toast
                        showNotification('Notification marked as seen successfully.','https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error marking notification as seen:', error);
                }
            });
        }
    </script>
</header>
