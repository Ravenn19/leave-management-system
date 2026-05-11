<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    // Employee: Submit leave request
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $user = $request->user();

        // Calculate total days
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $totalDays = $start->diff($end)->days + 1;

        // Check quota
        if (!$user->canTakeLeave($totalDays)) {
            return response()->json([
                'error' => 'Insufficient leave quota',
                'remaining_quota' => $user->remaining_quota,
                'requested_days' => $totalDays
            ], 422);
        }

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $leaveRequest = $user->leaveRequests()->create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Leave request submitted successfully',
            'data' => $leaveRequest
        ], 201);
    }

    // Employee: Get my leave requests
    public function myRequests(Request $request)
    {
        $requests = $request->user()->leaveRequests()
            ->with('approver')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $requests,
            'remaining_quota' => $request->user()->remaining_quota
        ]);
    }

    // Get specific request
    public function show(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with('user', 'approver')->findOrFail($id);

        // Check if user owns this request or is admin
        if ($leaveRequest->user_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($leaveRequest);
    }

    // Employee: Cancel pending request
    public function cancel(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        // Delete attachment if exists
        if ($leaveRequest->attachment) {
            Storage::disk('public')->delete($leaveRequest->attachment);
        }

        $leaveRequest->delete();

        return response()->json(['message' => 'Leave request cancelled successfully']);
    }

    // Admin: Get all leave requests
    public function allRequests(Request $request)
    {
        $query = LeaveRequest::with('user', 'approver');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($requests);
    }

    // Admin: Approve leave request
    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::where('status', 'pending')->findOrFail($id);

        $user = User::find($leaveRequest->user_id);

        // Double check quota
        if (!$user->canTakeLeave($leaveRequest->total_days)) {
            return response()->json([
                'error' => 'User does not have sufficient quota',
                'remaining_quota' => $user->remaining_quota
            ], 422);
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now()
        ]);

        return response()->json([
            'message' => 'Leave request approved successfully',
            'data' => $leaveRequest->load('user', 'approver')
        ]);
    }

    // Admin: Reject leave request
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $leaveRequest = LeaveRequest::where('status', 'pending')->findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'message' => 'Leave request rejected',
            'data' => $leaveRequest->load('user', 'approver')
        ]);
    }

    // Admin: Get statistics
    public function statistics(Request $request)
    {
        $stats = [
            'total_requests' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'total_days_approved' => LeaveRequest::where('status', 'approved')->sum('total_days'),
            'by_month' => LeaveRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->get(),
        ];

        return response()->json($stats);
    }
}
