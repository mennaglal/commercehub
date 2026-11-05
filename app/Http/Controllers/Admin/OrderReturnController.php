<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnRequest;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class OrderReturnController extends Controller implements HasMiddleware
{
    public function __construct(private ReturnService $returns)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:returns.view', only: ['index', 'show']),
            new Middleware('permission:returns.create', only: ['create', 'store']),
            // The refund step needs one more permission (checked inside updateStatus).
            new Middleware('permission:returns.update', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        // A filter value that is not in the list is ignored.
        $status = in_array($request->query('status'), OrderReturn::STATUSES, true) ? $request->query('status') : null;
        $search = trim((string) $request->query('search'));

        $returns = OrderReturn::with('order:id,order_number,customer_name')
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('return_number', 'like', "%{$search}%")
                                ->orWhereHas('order', function ($query) use ($search) {
                                    $query->where('order_number', 'like', "%{$search}%")
                                        ->orWhere('customer_name', 'like', "%{$search}%")
                                        ->orWhere('customer_phone', 'like', "%{$search}%");
                                });
                        });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.returns.index', ['returns' => $returns]);
    }

    // The form is opened from the order page.
    public function create(Order $order)
    {
        // Throws a validation error (and goes back) if the order is not delivered or the window is over.
        $lines = $this->returns->returnableItems($order);

        return view('admin.pages.returns.create', [
            'order' => $order,
            'lines' => $lines,
        ]);
    }

    public function store(ReturnRequest $request, Order $order)
    {
        $orderReturn = $this->returns->create($order, $request->validated(), $request->user()?->id);

        return redirect()
            ->route('admin.returns.show', $orderReturn)
            ->with('success', __('returns.messages.created'));
    }

    public function show(OrderReturn $orderReturn)
    {
        $orderReturn->load(['order:id,order_number,customer_name,customer_phone', 'items.orderItem']);

        return view('admin.pages.returns.show', ['orderReturn' => $orderReturn]);
    }

    public function updateStatus(Request $request, OrderReturn $orderReturn)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(OrderReturn::STATUSES)],
            // A rejection must have a reason.
            'admin_note' => ['nullable', 'string', 'max:1000', 'required_if:status,' . OrderReturn::STATUS_REJECTED],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'refund_reference' => ['nullable', 'string', 'max:255'],
            'restock' => ['nullable', 'array'],
            'restock.*' => ['boolean'],
        ]);

        // Moving money needs its own permission.
        if ($data['status'] === OrderReturn::STATUS_REFUNDED && ! $request->user()->can('returns.refund')) {
            abort(403);
        }

        $this->returns->changeStatus($orderReturn, $data['status'], $data, $request->user()?->id);

        return back()->with('success', __('returns.messages.updated'));
    }
}
