@php
    $wrapperTag = isset($link) ? 'a' : 'div';
    $wrapperAttrs = isset($link) ? 'href="' . $link . '"' : '';
@endphp
<{{ $wrapperTag }} {!! $wrapperAttrs !!} class="referral-user-cell @if(isset($link)) referral-user-cell--link @endif">
    <img src="{{ url($user->avatar ?? 'assets/img/default-avatar.webp') }}" alt="{{ $user->name }}" class="referral-user-cell__avatar">
    <div class="referral-user-cell__info">
        <span class="referral-user-cell__name">{{ $user->name }}</span>
        <span class="referral-user-cell__id">#{{ $user->id }}</span>
    </div>
</{{ $wrapperTag }}>
