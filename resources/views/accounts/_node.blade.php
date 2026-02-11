{{-- resources/views/accounts/_node.blade.php --}}
<li class="tree-node">
    <span class="acc-name">{{ $node['name'] }}</span>
    <span class="acc-code">({{ $node['acc_code'] }})</span>
    – 💰 {{ number_format($node['balance'], 2) }}

    @if (!empty($node['children']))
    <ul>
        @foreach ($node['children'] as $child)
        @include('accounts._node', ['node' => $child])
        @endforeach
    </ul>
    @endif
</li>
















