<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );
    }
}