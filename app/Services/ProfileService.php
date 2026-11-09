<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class ProfileService
{
    public function __construct(private ImageUploader $images)
    {
    }

    public function getProfile(User $user): User
    {
        return $user->load('store');
    }

    // $data is validated by ProfileUpdateRequest. Arr::only is a second guard: only these columns can change.
    public function updateProfile(User $user, array $data): User
    {
        $user->update(Arr::only($data, ['name', 'phone', 'country_id', 'city_id', 'area_id']));

        return $user->load('store');
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        $old = $user->avatar;

        $user->update(['avatar' => $this->images->upload($file, 'avatars')]);

        // Only after the new one is saved.
        $this->images->delete($old);

        return $user->load('store');
    }

    // The password is hashed by the "hashed" cast of the User model.
    // Every OTHER device is logged out (a stolen token stops working), the current one stays.
    public function changePassword(User $user, string $password): void
    {
        $user->update(['password' => $password]);

        $currentId = $user->currentAccessToken()?->id;

        $user->tokens()->when($currentId, fn ($query) => $query->where('id', '!=', $currentId))->delete();
    }
}
