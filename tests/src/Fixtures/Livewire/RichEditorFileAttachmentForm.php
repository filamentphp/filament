<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\MediaPostWithRichContent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RichEditorFileAttachmentForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $data = [];

    public ?int $recordId = null;

    public array $componentFileAttachments = [];

    public function mount(?int $recordId = null): void
    {
        $this->recordId = $recordId;
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('title'),
                RichEditor::make('content'),
            ])
            ->model($this->recordId ? MediaPostWithRichContent::find($this->recordId) : MediaPostWithRichContent::class)
            ->statePath('data');
    }

    public function createWithAttachments(array $attachmentIds, array $formData): void
    {
        foreach ($attachmentIds as $id) {
            $this->prepareFileAttachment($id);
        }

        $this->data = $formData;

        $data = $this->form->getState();
        $record = MediaPostWithRichContent::create($data);
        $this->form->model($record)->saveRelationships();
        $this->recordId = $record->getKey();
    }

    public function saveWithAttachments(array $attachmentIds, array $formData): void
    {
        foreach ($attachmentIds as $id) {
            $this->prepareFileAttachment($id);
        }

        $this->data = $formData;

        $record = MediaPostWithRichContent::find($this->recordId);
        $data = $this->form->getState();
        $record->update($data);
        $this->form->model($record)->saveRelationships();
    }

    protected function prepareFileAttachment(string $id): void
    {
        $image = imagecreatetruecolor(10, 10);
        ob_start();
        imagejpeg($image);
        $content = ob_get_clean();
        imagedestroy($image);

        Storage::disk('tmp-for-tests')->put('livewire-tmp/' . $id . '.jpg', $content);

        data_set(
            $this->componentFileAttachments,
            'data.content.' . $id,
            TemporaryUploadedFile::createFromLivewire($id . '.jpg'),
        );
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
