@php
    $avatarPath = public_path('assets/images/user/' . @$author->avatar);
    $hasAvatar = @$author->avatar && file_exists($avatarPath);
    $userName = $author->fullname ?? $author->username ?? 'User';
    // Generate avatar using DiceBear Avatars API (personas style - person face)
    $seed = md5($author->id . $author->username);
    $defaultAvatarUrl = 'https://api.dicebear.com/7.x/personas/svg?seed=' . $seed . '&backgroundColor=ff7c31&radius=50';
@endphp
@if($hasAvatar)
    <img src="{{ getImage(getFilePath('authorAvatar') . '/' . @$author->avatar) }}" 
         class="author-avatar" 
         alt="@lang('Author Image')">
@else
    <img src="{{ $defaultAvatarUrl }}"
         class="author-avatar" 
         alt="@lang('Author Image')"
         onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar.png') }}';">
@endif
