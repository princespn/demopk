@php
    $icons = getContent('social_icon.element', orderById:true);
    $contact = getContent('contact_us.content', true)->data_values;
@endphp
<header class="header">
    <div class="header__top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <ul class="header-info-list d-flex flex-wrap justify-content-lg-start justify-content-center">
                        <li>
                            <a href="mailto:{{ $contact->email_address }}"><i class="las la-envelope"></i>
                                {{ $contact->email_address }}
                            </a> 
                        </li>
                        <li>
                            <a href="tel:{{ $contact->contact_number }}"><i class="las la-phone-volume"></i>
                                {{ $contact->contact_number }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 d-flex flex-wrap align-items-center justify-content-lg-end justify-content-center mt-lg-0 mt-2">
                    <ul class="social-links style--white d-flex flex-wrap align-items-center justify-content-end">
                        <li class="font-size--14px text-white me-3">@lang('Social Links') :</li>
                        @foreach($icons as $icon)
                            <li>
                                <a target="_blank" href="{{ $icon->data_values->url }}">
                                    @php echo $icon->data_values->social_icon @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if($general->enable_language)
                        <select class="select select-sm style--trans w-auto ms-3 langSel">
                            @foreach ($language as $item)
                                <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                                    {{ __($item->name) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="header__bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-xl p-0 align-items-center">
                <a class="site-logo site-title" href="{{ route('home') }}">
                    <img src="{{getImage(getFilePath('logoIcon') .'/logo.png')}}" alt="@lang('logo')">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="menu-toggle"></span>
                </button>
                <div class="collapse navbar-collapse mt-lg-0 mt-3" id="navbarSupportedContent">
                    <ul class="navbar-nav main-menu ms-auto">
                        <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                        @php
                            $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', 0)->get();
                        @endphp
                        @foreach ($pages as $k => $data)
                            <li><a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                        @endforeach
                        <li><a href="{{ route('blogs') }}">@lang('Announcement')</a></li>
                        <li><a href="{{ route('api.documentation') }}">@lang('API Documentation')</a></li>
                        <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                    </ul>
                    <div class="nav-right">
                        @if(auth()->user())
                            <a href="{{ route('user.logout') }}" class="btn btn-sm btn--danger d-lg-inline-flex align-items-center me-2">
                                <i class="las la-sign-out-alt font-size--18px me-2"></i> @lang('Logout')
                            </a>
                            <a href="{{ route('user.home') }}" class="btn btn-sm btn--base d-lg-inline-flex align-items-center">
                                <i class="las la-home font-size--18px me-2"></i> @lang('Dashboard')
                            </a>
                        @elseif(agent())
                            <a href="{{ route('agent.logout') }}" class="btn btn-sm btn--danger d-lg-inline-flex align-items-center me-2">
                                <i class="las la-sign-out-alt font-size--18px me-2"></i> @lang('Logout')
                            </a>
                            <a href="{{ route('agent.home') }}" class="btn btn-sm btn--base d-lg-inline-flex align-items-center">
                                <i class="las la-home font-size--18px me-2"></i> @lang('Dashboard')
                            </a>
                        @elseif(merchant())
                            <a href="{{ route('merchant.logout') }}" class="btn btn-sm btn--danger d-lg-inline-flex align-items-center me-2">
                                <i class="las la-sign-out-alt font-size--18px me-2"></i> @lang('Logout')
                            </a>
                            <a href="{{ route('merchant.home') }}" class="btn btn-sm btn--base d-lg-inline-flex align-items-center">
                                <i class="las la-home font-size--18px me-2"></i> @lang('Dashboard')
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="btn btn-sm btn--base d-lg-inline-flex align-items-center">
                                <i class="las la-user-circle font-size--18px me-2"></i> @lang('Login')
                            </a>
                        @endif
                    </div>
                </div>
            </nav>
        </div>
    </div><!-- header__bottom end -->
</header>
