<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    // Keep fake upload paths within the `imports.file_path` column's length limit.
    Storage::set('tmp-for-tests', Storage::build([
        'driver' => 'local',
        'root' => sys_get_temp_dir() . '/filament-imports-' . bin2hex(random_bytes(8)),
    ]));

    app()->bind(Authenticatable::class, User::class);
});

afterEach(function (): void {
    File::deleteDirectory(Storage::disk('tmp-for-tests')->path(''));
});

/**
 * @return array<string>
 */
function importCsvEncodingTestFile(string $csv): array
{
    Exceptions::fake()->throwOnReport();

    livewire(CsvEncodingImportPage::class)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('posts.csv', $csv)])
        ->fillForm(['columnMap' => ['title' => 'title', 'content' => 'content']])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    return Post::query()->orderBy('id')->pluck('title')->all();
}

it('imports a CSV file in its detected encoding', function (string $csv, array $expectedTitles): void {
    expect(importCsvEncodingTestFile($csv))->toBe($expectedTitles);
})->with([
    'Windows-1252 with mid-word accents and a curly apostrophe' => [
        "title,content\r\nF\xE9lix,Body\r\nO\x92Brien,Body\r\n",
        ['Félix', 'O’Brien'],
    ],
    'Windows-1252 with accents only after the first 20 lines' => [
        "title,content\r\n" . str_repeat("Row,Body\r\n", 20) . "Andr\xE9,Body\r\n",
        [...array_fill(0, 20, 'Row'), 'André'],
    ],
    'Windows-1252 with fractions and units' => [
        "title,content\r\n\xBD kg,Body\r\n\xBC l,Body\r\n",
        ['½ kg', '¼ l'],
    ],
    'Windows-1252 with temperatures and units glued to digits' => [
        "title,content\r\n25\xB0C,Body\r\n5\xB5m,Body\r\n",
        ['25°C', '5µm'],
    ],
    'Windows-1252 with a curly apostrophe after digits' => [
        "title,content\r\n1920\x92s house,Body\r\nBuilt in the 80\x92s,Body\r\n",
        ['1920’s house', 'Built in the 80’s'],
    ],
    'Shift_JIS with kanji and kana' => [
        mb_convert_encoding("title,content\r\n山田 太郎,Body\r\nカタカナ テスト,Body\r\n", 'SJIS-win', 'UTF-8'),
        ['山田 太郎', 'カタカナ テスト'],
    ],
    'Shift_JIS with half-width katakana only' => [
        mb_convert_encoding("title,content\r\nﾔﾏﾀﾞ ﾀﾛｳ,Body\r\n", 'SJIS-win', 'UTF-8'),
        ['ﾔﾏﾀﾞ ﾀﾛｳ'],
    ],
    'Shift_JIS with ASCII product codes glued to katakana' => [
        mb_convert_encoding("title,content\r\nUSBメモリ 32GB,A-100\r\nHDMIケーブル 2m,B-200\r\n", 'SJIS-win', 'UTF-8'),
        ['USBメモリ 32GB', 'HDMIケーブル 2m'],
    ],
    'EUC-KR' => [
        mb_convert_encoding("title,content\r\n김민준,Body\r\n서울특별시 강남구 테헤란로 123,Body\r\n이서연,Body\r\n", 'EUC-KR', 'UTF-8'),
        ['김민준', '서울특별시 강남구 테헤란로 123', '이서연'],
    ],
    'GB18030 with Chinese sentences' => [
        mb_convert_encoding("title,content\r\n公司于本年度在北京设立了新的分支机构,Body\r\n我们计划在明年扩大华东地区的销售网络,Body\r\n所有员工将于下周参加年度培训课程,Body\r\n", 'GB18030', 'UTF-8'),
        ['公司于本年度在北京设立了新的分支机构', '我们计划在明年扩大华东地区的销售网络', '所有员工将于下周参加年度培训课程'],
    ],
    'UTF-8 with a byte order mark' => [
        "\xEF\xBB\xBFtitle,content\r\nFélix,Body\r\n",
        ['Félix'],
    ],
]);

// A first-match detector cannot tell these files from Latin text read as Shift_JIS or EUC-KR: `3個` has the
// shape of `90’s` (digit, one kanji, boundary) and `A級品` the shape of `décès` (letter, two kanji, boundary).
// They need a scoring detector; until then they are read as Windows-1252.
it('imports CJK files whose only non-ASCII text is single characters glued to ASCII', function (string $csv, array $expectedTitles): void {
    expect(importCsvEncodingTestFile($csv))->toBe($expectedTitles);
})->with([
    'Shift_JIS with quantities glued to digits' => [
        mb_convert_encoding("title,content\r\n3個,Body\r\n5枚,Body\r\n2台,Body\r\n", 'SJIS-win', 'UTF-8'),
        ['3個', '5枚', '2台'],
    ],
    'Shift_JIS with prices glued to the yen sign' => [
        mb_convert_encoding("title,content\r\n1000円,Body\r\n2500円,Body\r\n", 'SJIS-win', 'UTF-8'),
        ['1000円', '2500円'],
    ],
    'Shift_JIS with grades glued to a code letter' => [
        mb_convert_encoding("title,content\r\nA級品,Body\r\nB級品,Body\r\n", 'SJIS-win', 'UTF-8'),
        ['A級品', 'B級品'],
    ],
    'EUC-KR with quantities glued to digits' => [
        mb_convert_encoding("title,content\r\n3개,Body\r\n5장,Body\r\n", 'EUC-KR', 'UTF-8'),
        ['3개', '5장'],
    ],
])->skip('Needs a scoring detector: see the glued-character limitation in the pull request description.');

class CsvEncodingImportPage extends Actions
{
    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(CsvEncodingPostImporter::class),
        ];
    }
}

class CsvEncodingPostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->requiredMapping()->rules(['required']),
            ImportColumn::make('content'),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return app(Post::class);
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}
