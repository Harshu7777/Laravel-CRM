@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">Abandoned Cart Leads</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>User ID</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Email Sent</th>
                        <th>Abandoned At</th>
                        <th>Created</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leads as $index => $lead)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $lead->user_id }}</td>
                            <td>{{ $lead->product_id }}</td>
                            <td>{{ $lead->quantity }}</td>

                            <td>
                                @if($lead->email_sent)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>

                            <td>{{ $lead->abandoned_at }}</td>
                            <td>{{ $lead->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No abandoned carts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection