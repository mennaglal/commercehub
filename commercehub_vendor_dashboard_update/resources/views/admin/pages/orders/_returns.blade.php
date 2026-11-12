{{--
    Returns of an order. Put this line in orders/show.blade.php, before the "Totals" block:
        @include('admin.pages.orders._returns')
    (and add 'returns' to the $order->load([...]) list in OrderController::show)
--}}
<div class="card mb-3">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span>{{ __('returns.sections.returns') }}</span>

        @can('returns.create')
            @if($order->canBeReturned())
                <a href="{{ route('admin.orders.returns.create', $order) }}" class="btn btn-sm btn-outline-primary">
                    {{ __('returns.create') }}
                </a>
            @endif
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <tbody>
            @forelse($order->returns as $return)
                <tr>
                    <td>
                        <a href="{{ route('admin.returns.show', $return) }}">{{ $return->return_number }}</a>
                    </td>
                    <td>{{ __('returns.reasons.' . $return->reason) }}</td>
                    <td>{{ number_format($return->refund_amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $return->badge() }}">{{ __('returns.statuses.' . $return->status) }}</span>
                    </td>
                    <td>{{ $return->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td class="text-center text-muted py-3">{{ __('returns.none') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
