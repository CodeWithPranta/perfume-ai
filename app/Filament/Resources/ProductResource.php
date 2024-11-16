<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('products_for')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->reactive(), // Make reactive to trigger conditional questions
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\FileUpload::make('photos')
                    ->multiple()
                    ->required()
                    ->panelLayout('grid')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('question_answers')
                    ->schema(function (callable $get) {
                        // Retrieve the selected value from the `products_for` field
                        $productsFor = $get('products_for');

                        // Filter questions based on `products_for` value
                        $questions = Question::where('questions_for', $productsFor)->get();
                        $selectFields = [];

                        // Generate select fields only for the filtered questions
                        foreach ($questions as $question) {
                            $options = collect($question->options)
                                ->mapWithKeys(function ($option) {
                                    return [$option['name'] => $option['name']];
                                })
                                ->toArray();

                            $selectFields[] = Forms\Components\Select::make('question_' . $question->id)
                                ->label($question->title)
                                ->options($options)
                                ->required();
                        }

                        return $selectFields;
                    })
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('products_for')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
