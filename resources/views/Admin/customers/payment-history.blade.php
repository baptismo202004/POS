@extends('layouts.app')
@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="m-0">Payment History</h4>
                <p class="mb-0 text-muted">Track all customer payments and credit transactions</p>
                                        <th>Customer</th>
                                        <th>Payment Amount</th>
                                        <th>Payment Method</th>
                                        <th>Remaining Balance</th>
                                        <th>Cashier</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                            <td>{{ $payment->credit->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->credit->customer_name ?? 'Walk-in Customer' }}</td>
                                            <td>₱{{ number_format($payment->payment_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    {{ ucfirst($payment->payment_method) }}
                                                </span>
                                            </td>
                                            <td>₱{{ number_format($payment->remaining_balance_after_payment, 2) }}</td>
                                            <td>{{ $payment->cashier->name ?? 'Unknown' }}</td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No payment history found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($payments->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
