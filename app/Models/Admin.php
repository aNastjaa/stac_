<?php

namespace App\Models;

use App\Models\User;
use App\Models\SponsorSubmission;
use App\Models\Post;

class Admin
{
    // Create a new user (same as before)
    public static function createUser(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->roles()->attach($data['role']);

        return $user;
    }

    // Update user role (same as before)
    public static function updateUserRole(User $user, string $role): User
    {
        $user->roles()->sync([$role]);
        return $user;
    }

    // Delete a user (same as before)
    public static function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    // Update submission status
    public static function updateSubmissionStatus(SponsorSubmission $submission, string $status): SponsorSubmission
    {
        $submission->status = $status;
        $submission->save();

        return $submission;
    }

    // Update post status
    public static function updatePostStatus(Post $post, string $status): Post
    {
        $post->status = $status;
        $post->save();

        return $post;
    }

    // Accept a submission
    public static function acceptSubmission(SponsorSubmission $submission): SponsorSubmission
    {
        return self::updateSubmissionStatus($submission, 'accepted');
    }

    // Reject a submission
    public static function rejectSubmission(SponsorSubmission $submission): SponsorSubmission
    {
        return self::updateSubmissionStatus($submission, 'rejected');
    }

    // Accept a post
    public static function acceptPost(Post $post): Post
    {
        return self::updatePostStatus($post, 'posted');
    }

    // Reject a post
    public static function rejectPost(Post $post): Post
    {
        return self::updatePostStatus($post, 'rejected');
    }
}
