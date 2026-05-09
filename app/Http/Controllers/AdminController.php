<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Follow;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show followers management
     */
    public function followers()
    {
        $followers = Follow::with(['follower', 'followed'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.followers', compact('followers'));
    }

    /**
     * Show feedback management
     */
    public function feedback()
    {
        $feedbacks = Feedback::with(['narracion'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.feedback', compact('feedbacks'));
    }

    /**
     * Approve feedback
     */
    public function approveFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->aprobado = true;
        $feedback->save();

        return back()->with('success', 'Feedback aprobado exitosamente.');
    }

    /**
     * Reject feedback
     */
    public function rejectFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->aprobado = false;
        $feedback->save();

        return back()->with('success', 'Feedback rechazado.');
    }

    /**
     * Delete feedback
     */
    public function deleteFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return back()->with('success', 'Feedback eliminado permanentemente.');
    }
}
