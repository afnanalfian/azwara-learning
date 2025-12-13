<?php

if (! function_exists('toast')) {
    function toast(string $type, string $message): void
    {
        session()->flash('toasts', [
            [
                'type' => $type,
                'message' => $message,
            ]
        ]);
    }
}
