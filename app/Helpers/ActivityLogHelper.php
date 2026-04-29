<?php

use App\Models\ActivityLog;

function logActivity($action, $model, $metadata = [])
{
    ActivityLog::create([
        'store_id'   => auth()->user()->store->id ?? null,
        'user_id'    => auth()->id(),
        'action'     => $action,
        'model_type' => class_basename($model),
        'model_id'   => $model->id,
        'metadata'   => $metadata,
    ]);
}
