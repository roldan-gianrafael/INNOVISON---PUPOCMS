<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentAssistantController extends Controller
{
    public function index()
    {
        $assistants = User::query()
            ->whereIn('user_role', [User::ROLE_ADMIN, 'student_assistant', 'assistant', 'studentassistant'])
            ->latest()
            ->get();

        return view('admin.student-assistants', compact('assistants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'student_id' => $validated['student_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_role' => User::ROLE_ADMIN,
            'user_type' => 'Assistant',
        ]);

        return redirect()->route('admin.student-assistants.index')->with('success', 'Student assistant account created.');
    }

    public function update(Request $request, User $assistant): RedirectResponse
    {
        if (!$assistant->isStudentAssistant()) {
            abort(404);
        }

        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255', Rule::unique('users', 'student_id')->ignore($assistant->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($assistant->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $assistant->student_id = $validated['student_id'];
        $assistant->first_name = $validated['first_name'];
        $assistant->last_name = $validated['last_name'];
        $assistant->name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $assistant->email = $validated['email'];
        $assistant->user_role = User::ROLE_ADMIN;
        $assistant->user_type = $assistant->user_type ?: 'Assistant';

        if (!empty($validated['password'])) {
            $assistant->password = Hash::make($validated['password']);
        }

        $assistant->save();

        return redirect()->route('admin.student-assistants.index')->with('success', 'Student assistant account updated.');
    }

    public function destroy(User $assistant): RedirectResponse
    {
        if (!$assistant->isStudentAssistant()) {
            abort(404);
        }

        if ((int) Auth::id() === (int) $assistant->id) {
            return redirect()->route('admin.student-assistants.index')->withErrors([
                'assistant' => 'You cannot delete your own active account.',
            ]);
        }

        $assistant->delete();

        return redirect()->route('admin.student-assistants.index')->with('success', 'Student assistant account deleted.');
    }
}
