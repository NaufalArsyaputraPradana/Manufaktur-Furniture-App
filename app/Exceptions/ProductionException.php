<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductionException extends Exception
{
    /**
     * Report the exception
     */
    public function report(): void
    {
        Log::error('Production Error: ' . $this->message, [
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line,
        ]);
    }

    /**
     * Render the exception into an HTTP response
     */
    public function render()
    {
        return response()->view('errors.production', [
            'message' => $this->message,
        ], 400);
    }
}
