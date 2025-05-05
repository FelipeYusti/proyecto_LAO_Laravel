<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuth;

class Login extends BaseAuth
{

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make()
                ->schema([
                    $this->getLoginFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getRememberFormComponent(),
                ])
                ->columns(1)
        ])->statePath('data');
    }
    protected function getLoginFormComponent()
    {
        return TextInput::make('login')
            ->label('Login')
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $data['password'],
        ];
    }
    public function getTitle(): string
    {
        return 'Login '; // Oculta el texto "Sign in"
    }

    public function getHeading(): string
    {
        return ''; // Puedes personalizar esto
    }

    public function getLoginFormActionLabel(): string
    {
        return 'Iniciar sesión';
    }
}
