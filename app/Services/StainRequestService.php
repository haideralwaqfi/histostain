<?php

namespace App\Services;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use App\Models\User;
use App\Notifications\StainRequestTransitioned;
use App\Notifications\TechNotifyNewRequest;
use App\Notifications\TechNotifyRequestUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class StainRequestService
{
    /**
     * Create a new stain request, record the initial transition, attach files,
     * and notify techs.
     *
     * @param  array  $data   validated shared + type_data fields
     * @param  array  $files  TemporaryUploadedFile instances from Livewire
     */
    public function create(User $doctor, array $data, array $files = []): StainRequest
    {
        return DB::transaction(function () use ($doctor, $data, $files) {
            $request = StainRequest::create([
                'doctor_id'   => $doctor->id,
                'type'        => $data['type'],
                'status'      => StainRequestStatus::Pending,
                'priority'    => $data['priority'],
                'mrn'         => $data['mrn'] ?? null,
                'patient_name' => $data['patient_name'] ?? null,
                'case_number' => $data['case_number'],
                'notes'       => $data['notes'] ?? null,
                'type_data'   => $data['type_data'],
            ]);

            // Immutable creation record
            $request->transitions()->create([
                'from_status'     => null,
                'to_status'       => StainRequestStatus::Pending->value,
                'performed_by_id' => $doctor->id,
                'note'            => null,
                'created_at'      => now(),
            ]);

            // Attach requisition files to the media collection
            foreach ($files as $file) {
                $request->addMedia($file->getRealPath())
                    ->preservingOriginal()
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('requisition_attachments');
            }

            // Notify techs — STAT dispatches per-notifiable for priority handling
            $techs = User::techs()->approved()->whereNotNull('id')->get();

            if ($techs->isNotEmpty()) {
                if ($request->priority === StainRequestPriority::Stat) {
                    $techs->each(fn($tech) => $tech->notify(new TechNotifyNewRequest($request)));
                } else {
                    Notification::send($techs, new TechNotifyNewRequest($request));
                }
            }

            return $request;
        });
    }

    /**
     * Update an existing request's fields and optionally attach new files.
     *
     * @param  array  $data   validated shared + type_data fields
     * @param  array  $files  TemporaryUploadedFile instances from Livewire
     */
    public function update(StainRequest $request, array $data, array $files = []): void
    {
        $request->update([
            'priority'     => $data['priority'],
            'mrn'          => $data['mrn'] ?? null,
            'patient_name' => $data['patient_name'] ?? null,
            'case_number'  => $data['case_number'],
            'notes'        => $data['notes'] ?? null,
            'type_data'    => $data['type_data'],
        ]);

        foreach ($files as $file) {
            $request->addMedia($file->getRealPath())
                ->preservingOriginal()
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('requisition_attachments');
        }

        // Notify the assigned tech only if they have accepted the request
        $notifiableStatuses = [StainRequestStatus::Accepted, StainRequestStatus::InProgress];
        if ($request->assignedTech && in_array($request->status, $notifiableStatuses, strict: true)) {
            $request->assignedTech->notify(new TechNotifyRequestUpdated($request));
        }
    }

    /**
     * Advance a request to a new status, record the transition, and notify
     * the relevant parties.
     */
    public function transition(
        StainRequest $request,
        StainRequestStatus $target,
        User $actor,
        ?string $note = null
    ): void {
        $from = $request->status;

        DB::transaction(function () use ($request, $target, $actor, $note) {
            $request->transitionTo($target, $actor, $note);
        });

        // Always notify the doctor unless they performed the action themselves
        if ($request->doctor_id !== $actor->id) {
            $request->doctor->notify(new StainRequestTransitioned($request, $from, $target));
        }
    }

    /**
     * Assign a tech to a request (does not change status).
     */
    public function assign(StainRequest $request, User $tech, User $actor): void
    {
        $request->update(['assigned_tech_id' => $tech->id]);
    }
}
