@foreach ($subAccounts as $subAccount)
    @if ($subAccount['account'] == $parent_id)
        <option value="{{ $subAccount['id'] }}" style="padding-left: {{ $level * 15 }}px;" {{ isset($_GET['account']) && $_GET['account'] == $subAccount['id'] ? 'selected' : ''}}>
            {!! str_repeat('&nbsp;', $level * 4) . __($subAccount['name']) !!}
        </option>

        {{-- إعادة استدعاء الملف الجزئي لعرض الحسابات الفرعية الأخرى إذا كانت موجودة --}}
        @include('partials.sub-accounts', ['subAccounts' => $subAccounts, 'parent_id' => $subAccount['id'], 'level' => $level + 1])
    @endif
@endforeach
