<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\BestSellingProductTable;
use App\Filament\Pages\Dashboard;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets are now managed by the custom Dashboard class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                'panels::body.end',
                fn(): string => '
                    <div x-data="{
                        expanded: false,
                        toggleExpand() {
                            this.expanded = !this.expanded;
                        }
                    }" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
                        <!-- Expanded State -->
                        <div x-show="expanded"
                            x-cloak
                            x-transition:enter="fab-expand-enter"
                            x-transition:enter-start="fab-expand-enter-start"
                            x-transition:enter-end="fab-expand-enter-end"
                            x-transition:leave="fab-expand-leave"
                            x-transition:leave-start="fab-expand-leave-start"
                            x-transition:leave-end="fab-expand-leave-end"
                            style="position: relative;">
                            <a href="https://dewakoding.com/tutorial/source-code-toko-online-lengkap-payment-ekspedisi-otomatis"
                                target="_blank"
                                class="fab-link-admin"
                                style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; font-size: 14px; font-weight: 600; color: #ffffff; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 50px; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.5); text-decoration: none; transition: all 0.3s ease; white-space: nowrap;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Beli Source Code</span>
                            </a>
                            <button @click="toggleExpand()"
                                class="fab-close-admin"
                                style="position: absolute; top: -8px; right: -8px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; color: #ffffff; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4); cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 14px; height: 14px; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Collapsed State -->
                        <button x-show="!expanded"
                            x-cloak
                            @click="toggleExpand()"
                            class="fab-button-admin"
                            x-transition:enter="fab-collapse-enter"
                            x-transition:enter-start="fab-collapse-enter-start"
                            x-transition:enter-end="fab-collapse-enter-end"
                            x-transition:leave="fab-collapse-leave"
                            x-transition:leave-start="fab-collapse-leave-start"
                            x-transition:leave-end="fab-collapse-leave-end"
                            style="position: relative; display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; padding: 0; color: #ffffff; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 50%; border: none; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.6); cursor: pointer; overflow: hidden;">
                            <span style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #60a5fa; border-radius: 50%; opacity: 0.75; animation: ping-admin 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; pointer-events: none;"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 28px; height: 28px; z-index: 1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                    </div>
                    <style>
                        [x-cloak] { display: none !important; }

                        /* Expanded State Transitions */
                        .fab-expand-enter {
                            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        }
                        .fab-expand-enter-start {
                            opacity: 0;
                            transform: translateX(30px) scale(0.85);
                        }
                        .fab-expand-enter-end {
                            opacity: 1;
                            transform: translateX(0) scale(1);
                        }
                        .fab-expand-leave {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        }
                        .fab-expand-leave-start {
                            opacity: 1;
                            transform: translateX(0) scale(1);
                        }
                        .fab-expand-leave-end {
                            opacity: 0;
                            transform: translateX(30px) scale(0.85);
                        }

                        /* Collapsed State Transitions */
                        .fab-collapse-enter {
                            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        }
                        .fab-collapse-enter-start {
                            opacity: 0;
                            transform: scale(0.7) rotate(-180deg);
                        }
                        .fab-collapse-enter-end {
                            opacity: 1;
                            transform: scale(1) rotate(0deg);
                        }
                        .fab-collapse-leave {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        }
                        .fab-collapse-leave-start {
                            opacity: 1;
                            transform: scale(1) rotate(0deg);
                        }
                        .fab-collapse-leave-end {
                            opacity: 0;
                            transform: scale(0.7) rotate(180deg);
                        }

                        .fab-button-admin {
                            animation: fab-bounce-admin 1.5s ease-in-out infinite;
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        }

                        .fab-button-admin:hover {
                            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
                            box-shadow: 0 15px 35px -5px rgba(37, 99, 235, 0.8) !important;
                            transform: translateY(-3px) scale(1.05);
                            animation: none;
                        }

                        .fab-link-admin {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        }

                        .fab-link-admin:hover {
                            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
                            box-shadow: 0 15px 35px -5px rgba(37, 99, 235, 0.7) !important;
                            transform: translateY(-2px) scale(1.02);
                        }

                        .fab-close-admin:hover {
                            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
                            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.6) !important;
                            transform: scale(1.15) rotate(90deg);
                        }

                        .fab-close-admin:hover svg {
                            transform: rotate(90deg);
                        }

                        @keyframes fab-bounce-admin {
                            0%, 100% {
                                transform: translateY(0);
                            }
                            50% {
                                transform: translateY(-8px);
                            }
                        }

                        @keyframes ping-admin {
                            0% {
                                transform: scale(1);
                                opacity: 0.75;
                            }
                            75%, 100% {
                                transform: scale(2);
                                opacity: 0;
                            }
                        }
                    </style>
                '
            );
    }
}
