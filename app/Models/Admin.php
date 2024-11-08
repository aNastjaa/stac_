<?php

namespace App\Models;

use App\Models\User;
use App\Models\SponsorSubmission;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class Admin
{
    /**
     * Create a new user and assign a role.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public static function createUser(array $data): User
    {
        // Ensure role ID is passed, or retrieve it by role name
        $role = Role::where('name', $data['role'])->first();

        if (!$role) {
            throw new \Exception('Role not found');
        }

        // Create the user
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Attach the role to the user
        $user->roles()->attach($role->id);

        return $user;
    }

    /**
     * Update user role.
     *
     * @param  \App\Models\User  $user
     * @param  string  $role
     * @return \App\Models\User
     */
    public static function updateUserRole(User $user, string $role): User
    {
        // Ensure role ID is passed, or retrieve it by role name
        $role = Role::where('name', $role)->first();

        if (!$role) {
            throw new \Exception('Role not found');
        }

        // Sync the new role for the user
        $user->roles()->sync([$role->id]);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public static function deleteUser(User $user): bool
    {
        // Check if the user exists before deleting
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user->delete();
    }

    /**
     * Update submission status.
     *
     * @param  \App\Models\SponsorSubmission  $submission
     * @param  string  $status
     * @return \App\Models\SponsorSubmission
     */
    public static function updateSubmissionStatus(SponsorSubmission $submission, string $status): SponsorSubmission
    {
        $submission->status = $status;
        $submission->save();

        return $submission;
    }

    /**
     * Update post status.
     *
     * @param  \App\Models\Post  $post
     * @param  string  $status
     * @return \App\Models\Post
     */
    public static function updatePostStatus(Post $post, string $status): Post
    {
        $post->status = $status;
        $post->save();

        return $post;
    }

    /**
     * Accept a submission.
     *
     * @param  \App\Models\SponsorSubmission  $submission
     * @return \App\Models\SponsorSubmission
     */
    public static function acceptSubmission(SponsorSubmission $submission): SponsorSubmission
    {
        return self::updateSubmissionStatus($submission, 'accepted');
    }

    /**
     * Reject a submission.
     *
     * @param  \App\Models\SponsorSubmission  $submission
     * @return \App\Models\SponsorSubmission
     */
    public static function rejectSubmission(SponsorSubmission $submission): SponsorSubmission
    {
        return self::updateSubmissionStatus($submission, 'rejected');
    }

    /**
     * Accept a post.
     *
     * @param  \App\Models\Post  $post
     * @return \App\Models\Post
     */
    public static function acceptPost(Post $post): Post
    {
        return self::updatePostStatus($post, 'posted');
    }

    /**
     * Reject a post.
     *
     * @param  \App\Models\Post  $post
     * @return \App\Models\Post
     */
    public static function rejectPost(Post $post): Post
    {
        return self::updatePostStatus($post, 'rejected');
    }
}
