<?php

namespace Filament\Tests\Tables;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;

class TestOwnerModel extends Model
{
    protected $table = 'test_owners';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function items(): HasMany
    {
        return $this->hasMany(TestItemModel::class, 'owner_id');
    }
}

class TestItemModel extends Model
{
    protected $table = 'test_items';
    public $timestamps = false;
    protected $fillable = ['name', 'owner_id'];
}

uses(TestCase::class)->group('tables');

beforeEach(function () {
    $this->livewire = Mockery::mock(HasTable::class);

    Schema::create('test_owners', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();
    });

    Schema::create('test_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('owner_id')->nullable()->constrained('test_owners')->cascadeOnDelete();
        $table->string('name')->nullable();
    });
});

afterEach(function () {
    Mockery::close();
    Schema::dropIfExists('test_items');
    Schema::dropIfExists('test_owners');
});

it('can nullify table record action', function () {
    Table::configureUsing(fn (Table $table) => $table
        ->recordAction(null)
    );

    $table = Table::make($this->livewire);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->getRecordAction(new TestItemModel())->toBeNull();
});

it('can nullify table record url', function () {
    Table::configureUsing(fn (Table $table) => $table
        ->recordUrl(null)
    );

    $table = Table::make($this->livewire);

    expect($table)
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordUrl(new TestItemModel())->toBeNull();
});

it('can set table record action and url together', function () {
    Table::configureUsing(fn (Table $table) => $table
        ->recordAction('edit')
        ->recordUrl('/edit/1')
    );

    $table = Table::make($this->livewire);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordAction(new TestItemModel())->toBe('edit')
        ->getRecordUrl(new TestItemModel())->toBe('/edit/1');
});

it('can nullify table record action and url together', function () {
    Table::configureUsing(fn (Table $table) => $table
        ->recordAction(null)
        ->recordUrl(null)
    );

    $table = Table::make($this->livewire);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordAction(new TestItemModel())->toBeNull()
        ->getRecordUrl(new TestItemModel())->toBeNull();
});

it('respects closure values for record action and url', function () {
    $model = new TestItemModel();

    Table::configureUsing(fn (Table $table) => $table
        ->recordAction(fn (Model $record) => 'edit-' . $record::class)
        ->recordUrl(fn (Model $record) => '/edit/' . $record::class)
    );

    $table = Table::make($this->livewire);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordAction($model)->toBe('edit-' . $model::class)
        ->getRecordUrl($model)->toBe('/edit/' . $model::class);
});

it('respects nullified record url in relation manager', function () {
    $owner = TestOwnerModel::create(['name' => 'Test Owner']);

    $relationManager = Mockery::mock(RelationManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $relationManager->shouldReceive('getRelationship')
        ->andReturn($owner->items());

    $relationManager->shouldReceive('table')
        ->andReturnUsing(function (Table $table) {
            return $table->recordUrl(null);
        });

    $table = Table::make($relationManager);
    $table = $relationManager->table($table);

    expect($table)
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordUrl(new TestItemModel())->toBeNull();
});

it('respects nullified record action in relation manager', function () {
    $owner = TestOwnerModel::create(['name' => 'Test Owner']);

    $relationManager = Mockery::mock(RelationManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $relationManager->shouldReceive('getRelationship')
        ->andReturn($owner->items());

    $relationManager->shouldReceive('table')
        ->andReturnUsing(function (Table $table) {
            return $table->recordAction(null);
        });

    $table = Table::make($relationManager);
    $table = $relationManager->table($table);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->getRecordAction(new TestItemModel())->toBeNull();
});

it('respects custom record url in relation manager', function () {
    $owner = TestOwnerModel::create(['name' => 'Test Owner']);

    $relationManager = Mockery::mock(RelationManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $relationManager->shouldReceive('getRelationship')
        ->andReturn($owner->items());

    $relationManager->shouldReceive('table')
        ->andReturnUsing(function (Table $table) {
            return $table->recordUrl('/custom/url');
        });

    $table = Table::make($relationManager);
    $table = $relationManager->table($table);

    expect($table)
        ->hasRecordUrlBeenSet()->toBeTrue()
        ->getRecordUrl(new TestItemModel())->toBe('/custom/url');
});

it('respects custom record action in relation manager', function () {
    $owner = TestOwnerModel::create(['name' => 'Test Owner']);

    $relationManager = Mockery::mock(RelationManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $relationManager->shouldReceive('getRelationship')
        ->andReturn($owner->items());

    $relationManager->shouldReceive('table')
        ->andReturnUsing(function (Table $table) {
            return $table->recordAction('custom-action');
        });

    $table = Table::make($relationManager);
    $table = $relationManager->table($table);

    expect($table)
        ->hasRecordActionBeenSet()->toBeTrue()
        ->getRecordAction(new TestItemModel())->toBe('custom-action');
});
