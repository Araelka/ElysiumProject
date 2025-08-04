<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'editor' => \App\Http\Middleware\CheckEditor::class,
            'gameMaster' => \App\Http\Middleware\CheckGameMaster::class,
            'questionnaireSpecialist' => \App\Http\Middleware\CheckQuestionnaireSpecialist::class,
            'gameMasterorOrQuestionnaireSpecialist' => \App\Http\Middleware\CheckGameMasterOrQuestionnaireSpecialist::class,
            'player' => \App\Http\Middleware\CheckPlayer::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
