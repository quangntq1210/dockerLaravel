<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display user profile page.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $subscriber = $user ? $user->subscriber : null;
        $subscribedCampaigns = $subscriber
            ? $subscriber->campaigns()
                ->select('campaigns.*')
                ->distinct()
                ->orderByDesc('campaigns.created_at')
                ->paginate(6, ['campaigns.*'], 'subscribed_page')
            : collect();

        return view('user.profile', compact('user', 'subscribedCampaigns'));
    }

    /**
     * Update profile basic information and avatar.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user->name = $validated['name'];

        if ($request->hasFile('avatar')) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $googleDisk */
            $googleDisk = Storage::disk('google');
            $path = $request->file('avatar')->store('avatars', 'google');
            $googleDisk->setVisibility($path, 'public');
            $metadata = $googleDisk->getMetadata($path);
            $fileId = is_array($metadata) ? ($metadata['id'] ?? null) : null;
            $publicUrl = $googleDisk->url($path);
            $user->avatar_url = $fileId
                ? "https://drive.google.com/thumbnail?id={$fileId}&sz=w512"
                : ($publicUrl ?: $path);
        }

        $user->save();

        return back()->with('success', __('message.profile_updated_successfully'));
    }

    /**
     * Update profile password.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('message.current_password_incorrect')]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', __('message.password_updated_successfully'));
    }
}
