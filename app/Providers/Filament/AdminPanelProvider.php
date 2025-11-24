<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade; 

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->brandName("KEDAI NB'23")
            
            // --- CSS TAMPILAN FINAL ---
            ->renderHook(
                'panels::head.end',
                function (): string {
                    
                    // A. JIKA HALAMAN LOGIN -> HILANGKAN JUDUL "SIGN IN" & RAMPINGKAN
                    if (request()->routeIs('filament.admin.auth.login')) {
                        return Blade::render(<<<HTML
                            <style>
                                @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap');

                                body {
                                    background-color: #050505;
                                    background-image: radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000000 100%);
                                    height: 100vh; overflow: hidden;
                                }

                                /* Tulisan Background */
                                body::before {
                                    content: "KEDAI NB'23"; position: absolute; top: 10%; left: 50%; transform: translateX(-50%);
                                    font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 8vw; letter-spacing: 5px;
                                    color: transparent; -webkit-text-stroke: 2px rgba(245, 158, 11, 0.8);
                                    text-shadow: 0 0 80px rgba(245, 158, 11, 0.4);
                                    white-space: nowrap; z-index: 0; pointer-events: none;
                                }

                                /* Kotak Login */
                                .fi-simple-main {
                                    margin-top: 28vh !important; /* Turunkan lagi biar pas di tengah bawah */
                                    max-width: 400px !important; 
                                    width: 90% !important; 
                                    margin-inline: auto !important;
                                    
                                    background-color: rgba(0, 0, 0, 0.6) !important; 
                                    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                                    
                                    border: 1px solid rgba(255, 255, 255, 0.1) !important; 
                                    border-top: 1px solid rgba(245, 158, 11, 0.6) !important;
                                    
                                    border-radius: 24px; 
                                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8); 
                                    position: relative; z-index: 10;
                                    
                                    /* RAMPINGKAN KOTAK (Kurangi Padding) */
                                    padding-top: 1.5rem !important;
                                    padding-bottom: 1.5rem !important;
                                }

                                /* --- HILANGKAN TULISAN "SIGN IN" --- */
                                .fi-simple-header-heading {
                                    display: none !important;
                                }
                                /* ----------------------------------- */

                                /* Judul Brand Kecil di dalam kotak */
                                .fi-logo {
                                    color: #fbbf24 !important; 
                                    font-weight: 900 !important;
                                    font-size: 1.2rem !important;
                                    text-align: center;
                                    display: block;
                                    margin-bottom: 1rem;
                                }
                                
                                label span { color: #d1d5db !important; }

                                input { 
                                    background-color: rgba(30, 30, 30, 0.6) !important; 
                                    border-color: #333 !important; 
                                    color: white !important; 
                                    border-radius: 12px !important; 
                                    padding-top: 10px !important; 
                                    padding-bottom: 10px !important; 
                                }
                                input:focus { 
                                    border-color: #f59e0b !important; 
                                    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3) !important; 
                                }

                                button[type="submit"] { 
                                    background: linear-gradient(90deg, #d97706, #fbbf24) !important; 
                                    color: black !important; 
                                    font-weight: 900 !important; 
                                    border-radius: 12px !important; 
                                    letter-spacing: 1px; 
                                    box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.3); 
                                    transition: transform 0.2s; 
                                    margin-top: 1rem; /* Tambah jarak dikit */
                                }
                                button[type="submit"]:hover { transform: scale(1.02); }
                            </style>
                        HTML);
                    }

                    // B. JIKA DASHBOARD (NORMAL)
                    return Blade::render(<<<HTML
                        <style>
                            body {
                                background-color: #000000; 
                                background-image: radial-gradient(circle at 50% 50%, #141414 0%, #000000 100%);
                                min-height: 100vh; background-attachment: fixed;
                            }
                            .fi-sidebar, .fi-header { background-color: transparent !important; }
                            .fi-sidebar { border-right: 1px solid rgba(255, 255, 255, 0.05) !important; }
                        </style>
                    HTML);
                }
            )

            // --- 2. ALARM ---
            ->renderHook(
                'panels::body.end', 
                function (): string {
                    if (request()->routeIs('filament.admin.auth.login')) {
                        return '';
                    }
                    return Blade::render('@livewire("order-alert")');
                }
            )
            // ----------------

            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ]);
    }
}