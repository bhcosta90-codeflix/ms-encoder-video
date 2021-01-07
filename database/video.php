<?php
include __DIR__ . '/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('video_logs');
Capsule::schema()->dropIfExists('videos');

Capsule::schema()->create('videos', function ($table) {
    $table->id('id');
    $table->string('file');
    $table->string('field');
    $table->timestamps();
});

Capsule::schema()->create('video_logs', function ($table) {
    $table->id('id');
    $table->foreignId('video_id')->cosntrained('videos');
    $table->string('status');
    $table->timestamps();
});
