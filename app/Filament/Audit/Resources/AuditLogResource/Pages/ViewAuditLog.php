<?php

namespace App\Filament\Audit\Resources\AuditLogResource\Pages;

use App\Filament\Audit\Resources\AuditLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at'),
                        TextEntry::make('event_type'),
                        TextEntry::make('subject_type'),
                        TextEntry::make('subject_id'),
                        TextEntry::make('actor_id'),
                        TextEntry::make('actor_role'),
                        TextEntry::make('actor_type'),
                        TextEntry::make('ip_address')->visible(fn () => auth()->user()->isBpk()), // Task 1: BPK Full, ITJEN Limited
                        TextEntry::make('user_agent')->visible(fn () => auth()->user()->isBpk()),
                    ])->columns(2),
                Section::make('Changes')
                    ->schema([
                        KeyValueEntry::make('old_values'),
                        KeyValueEntry::make('new_values'),
                        KeyValueEntry::make('properties'),
                    ])
            ]);
    }
}
